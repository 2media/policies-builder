<?php

namespace Twomedia\PoliciesBuilder\Cms\Jigsaw;

use Closure;
use Illuminate\Support\Collection;
use Twomedia\PoliciesBuilder\Contracts\Policy;
use Twomedia\PoliciesBuilder\Contracts\PolicySource;
use Twomedia\PoliciesBuilder\DTOs\ResolvedPolicy;
use Twomedia\PoliciesBuilder\PoliciesConfiguration;
use Twomedia\PoliciesBuilder\Sources\LocalSnapshotPolicySource;
use Twomedia\PoliciesBuilder\Sources\RemotePolicySource;

class PoliciesCollection
{
    private Collection $jigsawConfig;

    private PoliciesConfiguration $policiesConfiguration;

    public function generate(Collection $config): Collection
    {
        $this->jigsawConfig = $config;
        $this->policiesConfiguration = $config->get('policies');

        $source = $this->resolveSource();

        return $this->getLanguagesToGenerate()
            ->map(fn (string $language) => $this->getPoliciesToGenerate()->map(function (Policy $policy) use ($language, $source) {
                $resolved = $source->resolve($policy, $language, $this->policiesConfiguration);

                return $this->generateInMemoryJigsawPage($policy, $resolved, $language);
            }))
            ->flatten(1);
    }

    private function resolveSource(): PolicySource
    {
        $snapshotPath = $this->policiesConfiguration['snapshotPath'] ?? null;

        if ($snapshotPath !== null) {
            return new LocalSnapshotPolicySource($snapshotPath);
        }

        return new RemotePolicySource($this->translationFunction());
    }

    private function getLanguagesToGenerate(): Collection
    {
        return collect($this->policiesConfiguration['languages']);
    }

    private function getPoliciesToGenerate(): Collection
    {
        return collect($this->policiesConfiguration['types']);
    }

    private function translationFunction(): ?Closure
    {
        return $this->jigsawConfig->get('transGlobal');
    }

    private function generateInMemoryJigsawPage(Policy $type, ResolvedPolicy $resolved, string $language): array
    {
        $toJigsawPage = new PolicyToInMemoryJigsawPage;

        /** @psalm-suppress InvalidArgument */
        return $toJigsawPage->generate($type, $resolved->content, $language, $resolved->metaTitle, $resolved->metaDescription);
    }
}
