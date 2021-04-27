<?php

namespace Twomedia\PoliciesBuilder\Policies;

use Twomedia\PoliciesBuilder\Contracts\CanBeBuiltInJigsaw;
use Twomedia\PoliciesBuilder\Contracts\Policy;

class ConditionsOfParticipation implements Policy, CanBeBuiltInJigsaw
{
    public array $placeholders = [
        //
    ];

    public static function make(): self
    {
        return new self();
    }

    public function endDate(string $endDate): self
    {
        $this->placeholders['end_date'] = $endDate;

        return $this;
    }

    public function type(): string
    {
        return 'conditions_of_participation';
    }

    public function metaTitleKey(): string
    {
        return 'global.conditionsOfParticipation';
    }

    public function jigsawPathName(): string
    {
        return 'conditions-of-participations';
    }

    public function placeholders(): array
    {
        return $this->placeholders;
    }
}
