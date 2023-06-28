<?php

namespace Twomedia\PoliciesBuilder\Policies;

use Twomedia\PoliciesBuilder\Contracts\CanBeBuiltInJigsaw;
use Twomedia\PoliciesBuilder\Contracts\Policy;
use Twomedia\PoliciesBuilder\DTOs\CooperationPartner;

class TermsOfService implements Policy, CanBeBuiltInJigsaw
{
    public array $placeholders = [
        //
    ];

    public static function make(): self
    {
        return new self();
    }

    public function placeholders(): array
    {
        return $this->placeholders;
    }

    public function inCooperationWith(CooperationPartner $partner): self
    {
        $this->placeholders['cooperation_partner_legal_name'] = $partner->legalName;
        $this->placeholders['cooperation_partner_name'] = $partner->name;
        $this->placeholders['cooperation_partner_url'] = $partner->url;

        return $this;
    }

    public function onBehalfOf(CooperationPartner $partner): self
    {
        $this->placeholders['behalf_of_legal_name'] = $partner->legalName;
        $this->placeholders['behalf_of_name'] = $partner->name;
        $this->placeholders['behalf_of_url'] = $partner->url;

        return $this;
    }

    public function jigsawPathName(): string
    {
        return 'terms';
    }

    public function type(): string
    {
        return 'terms';
    }

    public function metaTitleKey(): string
    {
        return 'global.terms';
    }
}
