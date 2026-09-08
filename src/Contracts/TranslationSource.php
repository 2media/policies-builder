<?php

namespace Twomedia\PoliciesBuilder\Contracts;

interface TranslationSource
{
    /**
     * Fetch all translation strings (key => value) for the given language.
     */
    public function fetch(string $language): array;
}
