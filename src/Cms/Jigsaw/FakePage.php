<?php

namespace Twomedia\PoliciesBuilder\Cms\Jigsaw;

class FakePage
{
    public string $lang;

    public function __construct(string $lang)
    {
        $this->lang = $lang;
    }
}
