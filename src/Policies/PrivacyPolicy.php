<?php

namespace Twomedia\PoliciesBuilder\Policies;

use Twomedia\PoliciesBuilder\Contracts\CanBeBuiltInJigsaw;
use Twomedia\PoliciesBuilder\Contracts\Policy;

class PrivacyPolicy implements CanBeBuiltInJigsaw, Policy
{
    public array $placeholders = [
        //
    ];

    public static function make(): self
    {
        return new self;
    }

    public function placeholders(): array
    {
        return [];
    }

    public function jigsawPathName(): string
    {
        return 'privacy';
    }

    public function type(): string
    {
        return 'privacy';
    }

    public function metaTitleKey(): string
    {
        return 'global.privacy';
    }
}
