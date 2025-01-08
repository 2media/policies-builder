<?php

namespace Twomedia\PoliciesBuilder\DTOs;

use Twomedia\PoliciesBuilder\Contracts\Stringable;

class IconCopyright implements Stringable
{
    public function __construct(public string $source) {}

    public static function make(string $source): IconCopyright
    {
        return new self(
            $source,
        );
    }

    public function __toString(): string
    {
        return "Icons © $this->source";
    }
}
