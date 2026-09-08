<?php

namespace Twomedia\PoliciesBuilder\Tests\Translations;

use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\Cms\Jigsaw\FakePage;
use Twomedia\PoliciesBuilder\Translations\GlobalTranslator;
use Twomedia\PoliciesBuilder\Translations\LocalSnapshotTranslationSource;

class GlobalTranslatorTest extends TestCase
{
    public Container $container;

    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container;

        $container['config'] = collect([
            'cache.default' => 'file',
            'cache.stores.file' => [
                'driver' => 'file',
                'path' => __DIR__.'/../../../storage/cache',
            ],
        ]);
        $container['files'] = new Filesystem;

        $cacheManager = new CacheManager($container);

        // Register CacheManager in Container
        $container->singleton(CacheManager::class, fn () => $cacheManager);

        $this->container = $container;
    }

    /** @test */
    public function it_returns_translated_string_from_global_translator_service()
    {
        /** @var GlobalTranslator $translator */
        $translator = $this->container->make(GlobalTranslator::class);

        $page = new FakePage('de');

        $translatedString = $translator->trans($page, 'global.imprint');

        $this->assertEquals('Impressum', $translatedString);
    }

    /** @test */
    public function it_reads_translations_from_a_local_snapshot_source_without_any_network_access()
    {
        $snapshotPath = sys_get_temp_dir().'/policies-builder-test-'.uniqid();
        mkdir($snapshotPath.'/translations', 0777, true);
        file_put_contents($snapshotPath.'/translations/de.json', json_encode([
            'global.imprint' => 'Impressum (snapshot)',
        ]));

        $translator = new GlobalTranslator(new LocalSnapshotTranslationSource($snapshotPath));

        $page = new FakePage('de');

        $this->assertEquals('Impressum (snapshot)', $translator->trans($page, 'global.imprint'));
    }
}
