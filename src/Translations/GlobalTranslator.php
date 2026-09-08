<?php

namespace Twomedia\PoliciesBuilder\Translations;

use Illuminate\Cache\CacheManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use LogicException;
use Twomedia\PoliciesBuilder\Contracts\TranslationSource;

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
        'pl',
    ];

    private readonly TranslationSource $source;

    public function __construct(?TranslationSource $source = null, ?CacheManager $cacheManager = null)
    {
        $this->source = $source ?? new RemoteTranslationSource($cacheManager);
    }

    /**
     * @return array|string
     */
    public function trans($page, string $key, array $replace = [])
    {
        $languageToTranslateTo = $page->lang ?? $page->fallbackLocale;

        if (! in_array($languageToTranslateTo, self::SUPPORTED_LANGUAGES)) {
            throw new LogicException("Language {$languageToTranslateTo} not supported");
        }

        $remoteLocaleStirngs = $this->source->fetch($languageToTranslateTo);

        $translator = $this->setupTranslator($languageToTranslateTo);

        $translator->addLines($remoteLocaleStirngs, $languageToTranslateTo);

        return $translator->get($key, $replace);
    }

    protected function setupTranslator(string $languageToTranslateTo): Translator
    {
        $filesystem = new Filesystem;
        $fileLoader = new FileLoader($filesystem, '.');

        return new Translator($fileLoader, $languageToTranslateTo);
    }
}
