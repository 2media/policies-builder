<?php

namespace Twomedia\PoliciesBuilder\Tests\Translations;

use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\Cms\Jigsaw\FakePage;
use Twomedia\PoliciesBuilder\Translations\GlobalTranslator;
use Twomedia\PoliciesBuilder\Translations\LocalSnapshotTranslationSource;

class GlobalTranslatorTest extends TestCase
{
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
