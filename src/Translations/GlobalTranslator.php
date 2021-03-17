<?php

namespace Twomedia\PoliciesBuilder\Translations;

use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
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

    public function __construct()
    {
    }

    public static function trans($page, string $key, array $replace = [])
    {
        $filesystem = new Filesystem();

        $fileLoader = new FileLoader($filesystem, '.');

        $languageToTranslateTo = $page->lang ?? $page->fallbackLocale ?? 'de';

        $translator = new Translator($fileLoader, $languageToTranslateTo);

        if (! in_array($languageToTranslateTo, self::SUPPORTED_LANGUAGES)) {
            throw new LogicException("Language {$languageToTranslateTo} not supported");
        }

        /** @var CacheManager $cache */
        /*$cache = Container::getInstance()->make(CacheManager::class);

        $cache = $cache->store();

        $remoteJson = $cache->remember('remote_json', 1000000000, function () {
            return json_decode(file_get_contents('http://127.0.0.1:8000/lang/policies.json'), true);
        });*/

        $remoteJson = json_decode(file_get_contents("http://127.0.0.1:8000/lang/{$languageToTranslateTo}/policies.json"), true);

        $translator->addLines($remoteJson, $languageToTranslateTo);

        /*foreach (self::SUPPORTED_LANGUAGES as $language) {
            $remoteJson = json_decode(file_get_contents("http://127.0.0.1:8000/lang/{$language}/policies.json"), true);
            $translator->addLines($remoteJson, $language);
        }*/

        return $translator->get($key, $replace);
    }
}
