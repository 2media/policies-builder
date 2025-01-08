<?php

namespace Twomedia\PoliciesBuilder\Cms\Jigsaw;

use Twomedia\PoliciesBuilder\Contracts\CanBeBuiltInJigsaw;

class PolicyToInMemoryJigsawPage
{
    public function generate(CanBeBuiltInJigsaw $policy, string $html, string $language, string $metaTitle): array
    {
        return [
            'extends' => '_layouts.policy',
            'lang' => $language,
            'locale' => $language,
            'meta_title' => $metaTitle,
            'meta_description' => '',
            'meta_description_key' => '',
            'content' => $html,

            // Unique InMemory Filename
            'filename' => "index-{$language}-".$policy->jigsawPathName(),

            // The `policy_type` must be defined like so. It can't be inlined in `path`
            'policy_type' => $policy->jigsawPathName(),
            'path' => '{locale}/{policy_type}',
        ];
    }
}
