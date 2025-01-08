<?php

namespace Twomedia\PoliciesBuilder\DTOs;

use Twomedia\PoliciesBuilder\Contracts\Stringable;

class Copyright implements Stringable
{
    public function __construct(
        public string $author,
        public string $source,
        public ?string $description = null
    ) {}

    public static function make(string $author, string $source, ?string $description = null): Copyright
    {
        return new self(
            $author,
            $source,
            $description
        );
    }

    public function __toString(): string
    {
        return "© {$this->author} / {$this->source}";
    }
}
