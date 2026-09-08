<?php

namespace Twomedia\PoliciesBuilder\Translations;

use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Container\Container;
use Twomedia\PoliciesBuilder\Contracts\TranslationSource;

/**
 * Fetches translation strings from the remote webservice.
 *
 * This is the historic (pre-snapshot) behaviour of `GlobalTranslator`,
 * extracted so it can be reused by the snapshot generation command and
 * kept as a fallback for projects that haven't opted into
 * `PoliciesConfiguration::snapshotPath()` yet.
 */
class RemoteTranslationSource implements TranslationSource
{
    const SECONDS_TO_CACHE_LOCALE_FILES = 60 * 60 * 24;

    private readonly CacheManager $cacheManager;

    public function __construct(?CacheManager $cacheManager = null)
    {
        if ($cacheManager === null) {
            /**
             * @var CacheManager $cacheManager
             *
             * @psalm-suppress UndefinedClass
             */
            $cacheManager = Container::getInstance()->make(CacheManager::class);
        }

        $this->cacheManager = $cacheManager;
    }

    public function fetch(string $language): array
    {
        // Make HTTP Request to Webservice, to fetch latest version of locale strings for the given language
        // The response is cached for x amount of seconds.
        return $this->cacheManager->remember(
            "trans::policies::{$language}",
            self::SECONDS_TO_CACHE_LOCALE_FILES,
            fn () => $this->fetchLocaleStringsForLanguage($language)
        );
    }

    protected function fetchLocaleStringsForLanguage(string $language): array
    {
        return json_decode(file_get_contents("https://v2.webservice.apy.ch/lang/{$language}/policies.json"), true);
    }
}
