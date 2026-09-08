<?php

namespace Twomedia\PoliciesBuilder\Tests\Cms\Jigsaw;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\Cms\Jigsaw\PoliciesCollection;
use Twomedia\PoliciesBuilder\Policies\Imprint;
use Twomedia\PoliciesBuilder\Policies\TermsOfService;
use Twomedia\PoliciesBuilder\PoliciesConfiguration;

class PoliciesCollectionSnapshotModeTest extends TestCase
{
    private string $snapshotPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->snapshotPath = sys_get_temp_dir().'/policies-builder-test-'.uniqid();

        foreach (['de', 'fr'] as $language) {
            mkdir("{$this->snapshotPath}/policies/{$language}", 0777, true);

            foreach (['terms' => TermsOfService::make(), 'imprint' => Imprint::make()] as $type => $policy) {
                file_put_contents("{$this->snapshotPath}/policies/{$language}/{$type}.json", json_encode([
                    'policy_type' => $type,
                    'locale' => $language,
                    'meta_title' => "{$type}-{$language}-title",
                    'meta_description' => '',
                    'content' => "<h1>{$type}-{$language}</h1>",
                ]));
            }
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->deleteDirectory($this->snapshotPath);
    }

    /** @test */
    public function it_reads_policies_from_the_snapshot_without_any_network_access()
    {
        $config = collect([
            'policies' => PoliciesConfiguration::make()
                ->languages(['de', 'fr'])
                ->domain('example.ch')
                ->snapshotPath($this->snapshotPath)
                ->types([
                    TermsOfService::make(),
                    Imprint::make(),
                ]),
        ]);

        $result = (new PoliciesCollection)->generate($config);

        $this->assertCount(4, $result);

        $germanImprint = $result->first(fn ($page) => $page['policy_type'] === 'imprint' && $page['locale'] === 'de');

        $this->assertEquals('imprint-de-title', $germanImprint['meta_title']);
        $this->assertEquals('<h1>imprint-de</h1>', $germanImprint['content']);
        $this->assertEquals('', $germanImprint['meta_description']);
        $this->assertEquals('{locale}/{policy_type}', $germanImprint['path']);
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
