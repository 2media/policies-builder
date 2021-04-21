<?php

namespace Twomedia\PoliciesBuilder\Contracts;

interface Policy
{
    public function type(): string;

    public function metaTitleKey(): string;

    public function placeholders(): array;
}
