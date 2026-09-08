<?php

namespace Twomedia\PoliciesBuilder\Snapshot;

use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Twomedia\PoliciesBuilder\Contracts\PolicySource;
use Twomedia\PoliciesBuilder\Contracts\TranslationSource;
use Twomedia\PoliciesBuilder\DTOs\ResolvedPolicy;
use Twomedia\PoliciesBuilder\PoliciesConfiguration;
use Twomedia\PoliciesBuilder\Sources\RemotePolicySource;
use Twomedia\PoliciesBuilder\Translations\GlobalTranslator;
use Twomedia\PoliciesBuilder\Translations\RemoteTranslationSource;

/**
 * Calls the remote webservice exactly once per configured language/policy
 * and writes the resolved data to committed JSON snapshot files, so that
 * subsequent Jigsaw builds (via `PoliciesConfiguration::snapshotPath()`)
 * never need to reach the webservice again.
 *
 * Usage (from a consuming lp-* project):
 *   vendor/bin/policies-snapshot --config=config.php --output=resources/policies-snapshot
 */
class SnapshotGenerator
{
    public function __construct(
        private readonly ?PolicySource $policySource = null,
        private readonly ?TranslationSource $translationSource = null,
    ) {}

    /**
     * @return array<int, string> Paths of all files written.
     */
    public function generate(PoliciesConfiguration $configuration, string $outputDir): array
    {
        $translationSource = $this->translationSource ?? $this->defaultTranslationSource();
        $policySource = $this->policySource ?? $this->defaultPolicySource($translationSource);

        $languages = $configuration['languages'] ?? [];
        $types = $configuration['types'] ?? [];

        $written = [];

        foreach ($languages as $language) {
            $written[] = $this->writeTranslations($translationSource, $language, $outputDir);

            foreach ($types as $policy) {
                $resolved = $policySource->resolve($policy, $language, $configuration);
                $written[] = $this->writePolicy($resolved, $outputDir);
            }
        }

        return $written;
    }

    private function defaultTranslationSource(): TranslationSource
    {
        // A throwaway, in-memory cache manager. We don't need the 24h file
        // cache here since this generator only ever runs once per invocation.
        $container = new Container;
        $container['config'] = collect([
            'cache.default' => 'array',
            'cache.stores.array' => ['driver' => 'array'],
        ]);

        return new RemoteTranslationSource(new CacheManager($container));
    }

    private function defaultPolicySource(TranslationSource $translationSource): PolicySource
    {
        $translator = new GlobalTranslator($translationSource);

        return new RemotePolicySource(
            fn ($page, $key, array $replace = []) => $translator->trans($page, $key, $replace)
        );
    }

    private function writeTranslations(TranslationSource $translationSource, string $language, string $outputDir): string
    {
        $strings = $translationSource->fetch($language);

        $path = rtrim($outputDir, '/')."/translations/{$language}.json";

        $this->put($path, $strings);

        return $path;
    }

    private function writePolicy(ResolvedPolicy $resolved, string $outputDir): string
    {
        $path = rtrim($outputDir, '/')."/policies/{$resolved->locale}/{$resolved->policyType}.json";

        $this->put($path, array_merge($resolved->toArray(), [
            'path' => "{$resolved->locale}/{$resolved->policyType}",
        ]));

        return $path;
    }

    private function put(string $path, array $data): void
    {
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents(
            $path,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n"
        );
    }
}
