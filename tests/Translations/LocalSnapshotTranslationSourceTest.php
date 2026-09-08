<?php

namespace Twomedia\PoliciesBuilder\Tests\Translations;

use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\Exceptions\SnapshotMissingException;
use Twomedia\PoliciesBuilder\Translations\LocalSnapshotTranslationSource;

class LocalSnapshotTranslationSourceTest extends TestCase
{
    private string $snapshotPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->snapshotPath = sys_get_temp_dir().'/policies-builder-test-'.uniqid();
        mkdir($this->snapshotPath.'/translations', 0777, true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->deleteDirectory($this->snapshotPath);
    }

    /** @test */
    public function it_reads_translation_strings_from_a_committed_snapshot_file()
    {
        file_put_contents($this->snapshotPath.'/translations/de.json', json_encode([
            'global.imprint' => 'Impressum',
        ]));

        $source = new LocalSnapshotTranslationSource($this->snapshotPath);

        $this->assertEquals(['global.imprint' => 'Impressum'], $source->fetch('de'));
    }

    /** @test */
    public function it_throws_a_clear_exception_when_the_snapshot_file_is_missing()
    {
        $source = new LocalSnapshotTranslationSource($this->snapshotPath);

        $this->expectException(SnapshotMissingException::class);
        $this->expectExceptionMessage('vendor/bin/policies-snapshot');

        $source->fetch('de');
    }

    private function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $items = array_diff(scandir($dir), ['.', '..']);

        foreach ($items as $item) {
            $path = "{$dir}/{$item}";
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
}
