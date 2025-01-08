<?php

namespace Twomedia\PoliciesBuilder\Tests;

use LogicException;
use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\CreatePayloadFromConfigurationAndPayload;
use Twomedia\PoliciesBuilder\Policies\TermsOfService;
use Twomedia\PoliciesBuilder\PoliciesConfiguration;

class CreatePayloadFromConfigurationAndPayloadTest extends TestCase
{
    /** @test */
    public function it_throws_logic_exception_if_domain_is_not_set_on_policies_configuration()
    {
        $this->expectException(LogicException::class);

        $configuration = PoliciesConfiguration::make();
        $termsOfService = TermsOfService::make();
        $language = 'de';

        (new CreatePayloadFromConfigurationAndPayload)->create($configuration, $termsOfService, $language);
    }

    /** @test */
    public function it_build_correct_payload_for_given_configuration_policy_and_language()
    {
        $configuration = PoliciesConfiguration::make()
            ->domain('2media.ch');
        $termsOfService = TermsOfService::make();
        $language = 'de';

        $payload = (new CreatePayloadFromConfigurationAndPayload)
            ->create($configuration, $termsOfService, $language);

        $this->assertEquals([
            'brand' => '2media',
            'variant' => 'default',
            'type' => $termsOfService->type(),
            'lang' => $language,
            'placeholders' => [
                'domain' => '2media.ch',
            ],
        ], $payload);
    }
}
