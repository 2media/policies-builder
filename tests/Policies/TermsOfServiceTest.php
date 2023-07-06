<?php

namespace Twomedia\PoliciesBuilder\Tests\Policies;

use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\DTOs\CooperationPartner;
use Twomedia\PoliciesBuilder\Policies\TermsOfService;

class TermsOfServiceTest extends TestCase
{
    /** @test */
    public function returns_empty_placeholder_array_if_no_setter_methods_are_called(): void
    {
        $termsOfService = TermsOfService::make();

        $this->assertEquals([], $termsOfService->placeholders());
    }

    /** @test */
    public function populates_cooperation_placeholders_if_cooperation_with_method_is_used(): void
    {
        $termsOfService = TermsOfService::make()
            ->inCooperationWith(CooperationPartner::make('Legal Name', 'Name', 'https://example.com'));

        $this->assertEquals([
            'cooperation_partner' => [
                'legal_name' => 'Legal Name',
                'name' => 'Name',
                'url' => 'https://example.com',
            ],
        ], $termsOfService->placeholders());
    }

    /** @test */
    public function populates_on_behalf_of_placeholders_if_on_behalf_of_method_is_used(): void
    {
        $termsOfService = TermsOfService::make()
            ->onBehalfOf(CooperationPartner::make('Legal Name', 'Name', 'https://example.com'));

        $this->assertEquals([
            'behalf_of' => [
                'legal_name' => 'Legal Name',
                'name' => 'Name',
                'url' => 'https://example.com',
            ],
        ], $termsOfService->placeholders());
    }
}
