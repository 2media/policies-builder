<?php

namespace Twomedia\PoliciesBuilder\Policies;

use Twomedia\PoliciesBuilder\Contracts\CanBeBuiltInJigsaw;
use Twomedia\PoliciesBuilder\Contracts\Policy;
use Twomedia\PoliciesBuilder\DTOs\CooperationPartner;

class ConditionsOfParticipation implements CanBeBuiltInJigsaw, Policy
{
    public array $placeholders = [
        //
    ];

    public static function make(): self
    {
        return new self;
    }

    public function closingDate(string $closingDate): self
    {
        $this->placeholders['closing_date'] = $closingDate;

        return $this;
    }

    public function type(): string
    {
        return 'conditions_of_participation';
    }

    public function metaTitleKey(): string
    {
        return 'global.conditions_of_participation';
    }

    public function jigsawPathName(): string
    {
        return 'conditions-of-participation';
    }

    public function placeholders(): array
    {
        return $this->placeholders;
    }

    public function onBehalfOf(CooperationPartner $partner): self
    {
        $this->placeholders['behalf_of'] = [
            'legal_name' => $partner->legalName,
            'name' => $partner->name,
            'url' => $partner->url,
        ];

        return $this;
    }
}
