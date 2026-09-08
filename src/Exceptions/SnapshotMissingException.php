<?php

namespace Twomedia\PoliciesBuilder\Exceptions;

use Exception;

class SnapshotMissingException extends Exception
{
    public static function forPolicy(string $snapshotPath, string $language, string $policyType): self
    {
        $expectedFile = rtrim($snapshotPath, '/')."/policies/{$language}/{$policyType}.json";

        return new self(
            "Snapshot file not found: {$expectedFile}. ".
            'Run `vendor/bin/policies-snapshot` to generate policy snapshots for this project.'
        );
    }

    public static function forTranslations(string $snapshotPath, string $language): self
    {
        $expectedFile = rtrim($snapshotPath, '/')."/translations/{$language}.json";

        return new self(
            "Snapshot file not found: {$expectedFile}. ".
            'Run `vendor/bin/policies-snapshot` to generate translation snapshots for this project.'
        );
    }
}
