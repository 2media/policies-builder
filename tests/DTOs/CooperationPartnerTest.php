<?php

namespace Twomedia\PoliciesBuilder\Tests\DTOs;

use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\DTOs\CooperationPartner;

class CooperationPartnerTest extends TestCase
{
    /** @test */
    public function generates_cooperation_partner_object_from_legal_name_name_and_url(): void
    {
        $cooperationPartner = CooperationPartner::make(legalName: '::legal-name::', name: '::name::', url: '::url::');

        $this->assertEquals('::legal-name::', $cooperationPartner->legalName);
        $this->assertEquals('::name::', $cooperationPartner->name);
        $this->assertEquals('::url::', $cooperationPartner->url);
    }
}
