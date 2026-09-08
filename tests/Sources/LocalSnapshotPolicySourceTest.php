<?php

namespace Twomedia\PoliciesBuilder\Tests\Sources;

use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\Exceptions\SnapshotMissingException;
use Twomedia\PoliciesBuilder\Policies\Imprint;
use Twomedia\PoliciesBuilder\PoliciesConfiguration;
use Twomedia\PoliciesBuilder\Sources\LocalSnapshotPolicySource;

class LocalSnapshotPolicySourceTest extends TestCase
{
    private string $snapshotPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->snapshotPath = sys_get_temp_dir().'/policies-builder-test-'.uniqid();
        mkdir($this->snapshotPath.'/policies/de', 0777, true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->deleteDirectory($this->snapshotPath);
    }

    /** @test */
    public function it_resolves_a_policy_from_a_committed_snapshot_file()
    {
        file_put_contents($this->snapshotPath.'/policies/de/imprint.json', json_encode([
            'policy_type' => 'imprint',
            'locale' => 'de',
            'meta_title' => 'Impressum',
            'meta_description' => '',
            'content' => '<h1>Impressum</h1>',
        ]));

        $source = new LocalSnapshotPolicySource($this->snapshotPath);

        $resolved = $source->resolve(Imprint::make(), 'de', PoliciesConfiguration::make());

        $this->assertEquals('imprint', $resolved->policyType);
        $this->assertEquals('de', $resolved->locale);
        $this->assertEquals('Impressum', $resolved->metaTitle);
        $this->assertEquals('', $resolved->metaDescription);
        $this->assertEquals('<h1>Impressum</h1>', $resolved->content);
    }

    /** @test */
    public function it_throws_a_clear_exception_when_the_snapshot_file_is_missing()
    {
        $source = new LocalSnapshotPolicySource($this->snapshotPath);

        $this->expectException(SnapshotMissingException::class);
        $this->expectExceptionMessage('vendor/bin/policies-snapshot');

        $source->resolve(Imprint::make(), 'de', PoliciesConfiguration::make());
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
