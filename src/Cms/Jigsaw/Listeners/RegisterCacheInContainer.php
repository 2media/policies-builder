<?php

namespace Twomedia\PoliciesBuilder\Cms\Jigsaw\Listeners;

use Illuminate\Cache\CacheManager;
use Illuminate\Filesystem\Filesystem;
use TightenCo\Jigsaw\Jigsaw;

class RegisterCacheInContainer
{
    /**
     * @psalm-suppress UndefinedInterfaceMethod
     */
    public function handle(Jigsaw $jigsaw): void
    {
        $container = $jigsaw->app;

        $container['config'] = $container['config']->merge(collect([
            'cache.default' => 'file',
            'cache.stores.file' => [
                'driver' => 'file',
                'path' => __DIR__ . '/storage/cache',
            ],
        ]));

        $container['files'] = new Filesystem;

        $container->singleton(CacheManager::class, function () use ($container) {
            return new CacheManager($container);
        });
    }
}
