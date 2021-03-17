<?php

namespace Twomedia\PoliciesBuilder\Policies;

use Twomedia\PoliciesBuilder\Contracts\CanBeBuiltInJigsaw;
use Twomedia\PoliciesBuilder\Contracts\Policy;

class TermsOfService implements Policy, CanBeBuiltInJigsaw
{
    public array $placeholders = [
        //
    ];

    public static function make()
    {
        return new self();
    }

    public function placeholders(): array
    {
        return [];
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
