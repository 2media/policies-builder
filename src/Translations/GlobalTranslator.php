<?php

namespace Twomedia\PoliciesBuilder\Translations;

use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use LogicException;

class GlobalTranslator
{
    const SUPPORTED_LANGUAGES = [
        'de',
        'fr',
        'it',
        'en',
        'es',
        'pt',
        'sr',
        'sq',
        'tr',
    ];

    const SECONDS_TO_CACHE_LOCALE_FILES = 60 * 60 * 24;

    /**
     * @var CacheManager|null
     */
    private ?CacheManager $cacheManager;

    public function __construct(CacheManager $cacheManager = null)
    {
        if ($cacheManager === null) {
            /**
             * @var CacheManager $cache
             * @psalm-suppress UndefinedClass
             */
            $cacheManager = Container::getInstance()->make(CacheManager::class);
        }

        $this->cacheManager = $cacheManager;
    }

    /**
     * @param $page
     * @param string $key
     * @param array $replace
     * @return array|string
     */
    public function trans($page, string $key, array $replace = [])
    {
        $languageToTranslateTo = $page->lang ?? $page->fallbackLocale;

        if (! in_array($languageToTranslateTo, self::SUPPORTED_LANGUAGES)) {
            throw new LogicException("Language {$languageToTranslateTo} not supported");
        }

        // Make HTTP Request to Webservice, to fetch latest version of locale strings for the given language
        // The response is cached for x amount of seconds.
        $remoteLocaleStirngs = $this->cacheManager->remember(
            "trans::policies::{$languageToTranslateTo}",
            self::SECONDS_TO_CACHE_LOCALE_FILES,
            fn () => $this->fetchLocaleStringsForLanguage($languageToTranslateTo)
        );

        $translator = $this->setupTranslator($languageToTranslateTo);

        $translator->addLines($remoteLocaleStirngs, $languageToTranslateTo);

        return $translator->get($key, $replace);
    }

    /**
     * @param string $language
     * @return array
     */
    protected function fetchLocaleStringsForLanguage(string $language): array
    {
        return json_decode(file_get_contents("https://v2.webservice.apy.ch/lang/{$language}/policies.json"), true);
    }

    /**
     * @param string $languageToTranslateTo
     * @return Translator
     */
    protected function setupTranslator(string $languageToTranslateTo): Translator
    {
        $filesystem = new Filesystem();
        $fileLoader = new FileLoader($filesystem, '.');

        return new Translator($fileLoader, $languageToTranslateTo);
    }
}
