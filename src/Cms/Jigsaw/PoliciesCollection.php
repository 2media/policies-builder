<?php

namespace Twomedia\PoliciesBuilder\Cms\Jigsaw;

use Illuminate\Support\Collection;
use LogicException;
use Twomedia\PoliciesBuilder\Contracts\Policy;
use Twomedia\PoliciesBuilder\DTOs\ResolvedPolicy;
use Twomedia\PoliciesBuilder\PoliciesConfiguration;
use Twomedia\PoliciesBuilder\Sources\LocalSnapshotPolicySource;

class PoliciesCollection
{
    private PoliciesConfiguration $policiesConfiguration;

    public function generate(Collection $config): Collection
    {
        $this->policiesConfiguration = $config->get('policies');

        $source = new LocalSnapshotPolicySource($this->snapshotPath());

        return $this->getLanguagesToGenerate()
            ->map(fn (string $language) => $this->getPoliciesToGenerate()->map(function (Policy $policy) use ($language, $source) {
                $resolved = $source->resolve($policy, $language, $this->policiesConfiguration);

                return $this->generateInMemoryJigsawPage($policy, $resolved, $language);
            }))
            ->flatten(1);
    }

    private function snapshotPath(): string
    {
        $snapshotPath = $this->policiesConfiguration['snapshotPath'] ?? null;

        if ($snapshotPath === null) {
            throw new LogicException(
                'PoliciesConfiguration::snapshotPath() is not set. Define one and generate the '.
                'snapshot with `vendor/bin/policies-snapshot` (see the "Snapshot Mode" section '.
                'of the README for the version of this package that still supports it).'
            );
        }

        return $snapshotPath;
    }

    private function getLanguagesToGenerate(): Collection
    {
        return collect($this->policiesConfiguration['languages']);
    }

    private function getPoliciesToGenerate(): Collection
    {
        return collect($this->policiesConfiguration['types']);
    }

    private function generateInMemoryJigsawPage(Policy $type, ResolvedPolicy $resolved, string $language): array
    {
        $toJigsawPage = new PolicyToInMemoryJigsawPage;

        /** @psalm-suppress InvalidArgument */
        return $toJigsawPage->generate($type, $resolved->content, $language, $resolved->metaTitle, $resolved->metaDescription);
    }
}
