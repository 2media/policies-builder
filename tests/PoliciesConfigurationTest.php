<?php

namespace Twomedia\PoliciesBuilder\Tests;

use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\Policies\TermsOfService;
use Twomedia\PoliciesBuilder\PoliciesConfiguration;

class PoliciesConfigurationTest extends TestCase
{
    /** @test */
    public function configuration_can_be_defined_by_using_fluent_methods()
    {
        // configuration can be defined by using fluent methods
        $config = PoliciesConfiguration::make()
            ->domain('2media.ch')
            ->brand('example')
            ->variant('version-2')
            ->languages(['de', 'fr', 'some-string'])
            ->types([
                TermsOfService::make(),
                'example',
            ]);

        $configAsArray = $config->toArray();

        $this->assertEquals([
            'brand' => 'example',
            'variant' => 'version-2',
            'domain' => '2media.ch',
            'languages' => [
                'de',
                'fr',
                'some-string',
            ],
            'types' => [
                TermsOfService::make(),
                'example',
            ],
        ], $configAsArray);
    }

    /** @test */
    public function snapshot_path_can_be_configured()
    {
        $config = PoliciesConfiguration::make()
            ->snapshotPath('resources/policies-snapshot');

        $this->assertEquals('resources/policies-snapshot', $config['snapshotPath']);
    }
}
