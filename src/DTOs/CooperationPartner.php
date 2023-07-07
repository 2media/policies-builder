<?php

namespace Twomedia\PoliciesBuilder\DTOs;

class CooperationPartner
{
    public function __construct(
        public readonly string $legalName,
        public readonly string $name,
        public readonly string $url,
    ) {
        //
    }

    public static function make(string $legalName, string $name, string $url): self
    {
        return new self(
            legalName: $legalName,
            name: $name,
            url: $url,
        );
    }
}
