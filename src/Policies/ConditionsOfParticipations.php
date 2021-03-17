<?php

namespace Twomedia\PoliciesBuilder\Policies;

use Twomedia\PoliciesBuilder\Contracts\CanBeBuiltInJigsaw;
use Twomedia\PoliciesBuilder\Contracts\Policy;

class ConditionsOfParticipations implements Policy, CanBeBuiltInJigsaw
{
    public array $placeholders = [
        //
    ];

    public static function make()
    {
        return new self();
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
        return [];
    }
}
