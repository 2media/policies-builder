<?php

namespace Twomedia\PoliciesBuilder\DTOs;

use Stringable;

class Copyright implements Stringable
{
    public string $author;

    public string $source;

    public ?string $description;

    public function __construct(string $author, string $source, string $description = null)
    {
        $this->author = $author;
        $this->source = $source;
        $this->description = $description;
    }

    public static function make(string $author, string $source, string $description = null): Copyright
    {
        return new self(
            $author,
            $source,
            $description
        );
    }

    public function __toString()
    {
        return "© {$this->author} / {$this->source}";
    }
}
