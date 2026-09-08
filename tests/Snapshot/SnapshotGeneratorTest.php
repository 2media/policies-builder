<?php

namespace Twomedia\PoliciesBuilder\Tests\Snapshot;

use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\Contracts\Policy;
use Twomedia\PoliciesBuilder\Contracts\PolicySource;
use Twomedia\PoliciesBuilder\Contracts\TranslationSource;
use Twomedia\PoliciesBuilder\DTOs\ResolvedPolicy;
use Twomedia\PoliciesBuilder\Policies\Imprint;
use Twomedia\PoliciesBuilder\Policies\TermsOfService;
use Twomedia\PoliciesBuilder\PoliciesConfiguration;
use Twomedia\PoliciesBuilder\Snapshot\SnapshotGenerator;

class SnapshotGeneratorTest extends TestCase
{
    private string $outputDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->outputDir = sys_get_temp_dir().'/policies-builder-test-'.uniqid();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->deleteDirectory($this->outputDir);
    }

    /** @test */
    public function it_writes_a_policy_and_translations_file_per_language_without_touching_the_real_webservice()
    {
        $configuration = PoliciesConfiguration::make()
            ->domain('example.ch')
            ->languages(['de', 'fr'])
            ->types([
                TermsOfService::make(),
                Imprint::make(),
            ]);

        $policySource = new class implements PolicySource
        {
            public function resolve(Policy $policy, string $language, PoliciesConfiguration $configuration): ResolvedPolicy
            {
                return new ResolvedPolicy(
                    policyType: $policy->type() === 'terms' ? 'terms' : 'imprint',
                    locale: $language,
                    metaTitle: "{$policy->type()}-{$language}-title",
                    metaDescription: '',
                    content: "<h1>{$policy->type()}-{$language}</h1>",
                );
            }
        };

        $translationSource = new class implements TranslationSource
        {
            public function fetch(string $language): array
            {
                return ['global.imprint' => "Impressum-{$language}"];
            }
        };

        $written = (new SnapshotGenerator($policySource, $translationSource))->generate($configuration, $this->outputDir);

        // 2 languages * (1 translations file + 2 policy files) = 6 files
        $this->assertCount(6, $written);

        $imprintDe = json_decode(file_get_contents("{$this->outputDir}/policies/de/imprint.json"), true);

        $this->assertEquals([
            'policy_type' => 'imprint',
            'locale' => 'de',
            'meta_title' => 'imprint-de-title',
            'meta_description' => '',
            'content' => '<h1>imprint-de</h1>',
            'path' => 'de/imprint',
        ], $imprintDe);

        $translationsDe = json_decode(file_get_contents("{$this->outputDir}/translations/de.json"), true);

        $this->assertEquals(['global.imprint' => 'Impressum-de'], $translationsDe);
    }

    private function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $items = array_diff(scandir($dir), ['.', '..']);

        foreach ($items as $item) {
            $path = "{$dir}/{$item}";
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
}
