<?php

namespace Twomedia\PoliciesBuilder\Translations;

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

    public function __construct(private readonly TranslationSource $source) {}

    /**
     * @return array|string
     */
    public function trans($page, string $key, array $replace = [])
    {
        $languageToTranslateTo = $page->lang ?? $page->fallbackLocale;

        if (! in_array($languageToTranslateTo, self::SUPPORTED_LANGUAGES)) {
            throw new LogicException("Language {$languageToTranslateTo} not supported");
        }

        $localeStrings = $this->source->fetch($languageToTranslateTo);

        $translator = $this->setupTranslator($languageToTranslateTo);

        $translator->addLines($localeStrings, $languageToTranslateTo);

        return $translator->get($key, $replace);
    }

    protected function setupTranslator(string $languageToTranslateTo): Translator
    {
        $filesystem = new Filesystem;
        $fileLoader = new FileLoader($filesystem, '.');

        return new Translator($fileLoader, $languageToTranslateTo);
    }
}
