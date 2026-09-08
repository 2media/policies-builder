<?php

namespace Twomedia\PoliciesBuilder\Sources;

use Twomedia\PoliciesBuilder\Contracts\Policy;
use Twomedia\PoliciesBuilder\Contracts\PolicySource;
use Twomedia\PoliciesBuilder\DTOs\ResolvedPolicy;
use Twomedia\PoliciesBuilder\Exceptions\SnapshotMissingException;
use Twomedia\PoliciesBuilder\PoliciesConfiguration;

/**
 * Resolves a Policy by reading a previously generated, committed JSON
 * snapshot file from disk. No network access is performed.
 *
 * Snapshot files are expected at:
 *   {snapshotPath}/policies/{language}/{policyType}.json
 *
 * Generate/refresh them with `vendor/bin/policies-snapshot`.
 */
class LocalSnapshotPolicySource implements PolicySource
{
    public function __construct(private readonly string $snapshotPath) {}

    public function resolve(Policy $policy, string $language, PoliciesConfiguration $configuration): ResolvedPolicy
    {
        $policyType = method_exists($policy, 'jigsawPathName') ? $policy->jigsawPathName() : $policy->type();

        $file = rtrim($this->snapshotPath, '/')."/policies/{$language}/{$policyType}.json";

        if (! is_file($file)) {
            throw SnapshotMissingException::forPolicy($this->snapshotPath, $language, $policyType);
        }

        $data = json_decode(file_get_contents($file), true);

        return ResolvedPolicy::fromArray($data);
    }
}
