<?php

namespace Twomedia\PoliciesBuilder\Translations;

use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

class GlobalTranslator
{
    public const PATH_TO_TRANSLATION = './policiesPackage/resources/lang/';

    public static function trans($page, string $key, array $replace = [])
    {
        $filesystem = new Filesystem();

        $fileLoader = new FileLoader($filesystem, self::PATH_TO_TRANSLATION);

        $translator = new Translator($fileLoader, $page->lang ?? $page->fallbackLocale ?? 'de');

        /** @var CacheManager $cache */
        $cache = Container::getInstance()->make(CacheManager::class);

        $cache = $cache->store();

        $remoteJson = $cache->remember('remote_json', 1000000000, function () {
            return json_decode(file_get_contents('http://127.0.0.1:8000/lang/policies.json'), true);
        });

         $translator->addLines($remoteJson, 'de');


        return $translator->get($key, $replace);
    }
}
