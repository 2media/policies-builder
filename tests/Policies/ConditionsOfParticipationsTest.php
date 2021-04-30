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
    public function can_set_end_date_onf_conditions_of_participation()
    {
        $policy = ConditionsOfParticipation::make()
            ->closingDate('31.12.2021');

        $this->assertNotEmpty($policy->placeholders());

        $this->assertEquals([
            'closing_date' => '31.12.2021',
        ], $policy->placeholders());
    }
}
