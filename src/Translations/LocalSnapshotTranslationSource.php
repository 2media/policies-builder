<?php

namespace Twomedia\PoliciesBuilder\Translations;

use Twomedia\PoliciesBuilder\Contracts\TranslationSource;
use Twomedia\PoliciesBuilder\Exceptions\SnapshotMissingException;

/**
 * Reads translation strings from a previously generated, committed JSON
 * snapshot file. No network access is performed.
 *
 * Snapshot files are expected at:
 *   {snapshotPath}/translations/{language}.json
 *
 * Generate/refresh them with `vendor/bin/policies-snapshot`.
 */
class LocalSnapshotTranslationSource implements TranslationSource
{
    public function __construct(private readonly string $snapshotPath) {}

    public function fetch(string $language): array
    {
        $file = rtrim($this->snapshotPath, '/')."/translations/{$language}.json";

        if (! is_file($file)) {
            throw SnapshotMissingException::forTranslations($this->snapshotPath, $language);
        }

        return json_decode(file_get_contents($file), true);
    }
}
