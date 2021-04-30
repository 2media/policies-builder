<?php

namespace Twomedia\PoliciesBuilder\DTOs;

use Twomedia\PoliciesBuilder\Contracts\Stringable;

class IconCopyright implements Stringable
{
    public string $source;

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    public static function make(string $source): IconCopyright
    {
        return new self(
            $source,
        );
    }

    public function __toString()
    {
        return "Icons © $this->source";
    }
}
