<?php

namespace Twomedia\PoliciesBuilder\DTOs;

class IconCopyright
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

    public function toString(): string
    {
        return "Icons © $this->source";
    }
}
