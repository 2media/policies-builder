<?php

namespace Twomedia\PoliciesBuilder\Tests\Policies;

use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\Policies\ConditionsOfParticipation;

class ConditionsOfParticipationsTest extends TestCase
{
    /** @test */
    public function the_placeholders_are_by_default_empty_for_conditions_of_participations()
    {
        $policy = ConditionsOfParticipation::make();

        $this->assertEmpty($policy->placeholders());
    }

    /** @test */
    public function can_set_closing_date_on_conditions_of_participation()
    {
        $policy = ConditionsOfParticipation::make()
            ->closingDate('31.12.2021');

        $this->assertNotEmpty($policy->placeholders());

        $this->assertEquals([
            'closing_date' => '31.12.2021',
        ], $policy->placeholders());
    }

    /** @test */
    public function populates_on_behalf_of_placeholders_if_on_behalf_of_method_is_used(): void
    {
        $policy = ConditionsOfParticipation::make()
            ->onBehalfOf(CooperationPartner::make('Legal Name', 'Name', 'https://example.com'));

        $this->assertEquals([
            'behalf_of' => [
                'legal_name' => 'Legal Name',
                'name' => 'Name',
                'url' => 'https://example.com',
            ],
        ], $policy->placeholders());
    }
}
