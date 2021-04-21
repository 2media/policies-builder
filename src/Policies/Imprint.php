<?php

namespace Twomedia\PoliciesBuilder\Policies;

use Twomedia\PoliciesBuilder\Contracts\CanBeBuiltInJigsaw;
use Twomedia\PoliciesBuilder\Contracts\Policy;
use Twomedia\PoliciesBuilder\DTOs\Copyright;

class Imprint implements Policy, CanBeBuiltInJigsaw
{
    public array $placeholders = [
        //
    ];

    public static function make(): self
    {
        return new self();
    }

    /**
     * @param array<Copyright> $copyrights
     * @return $this
     */
    public function imageCopyrights(array $copyrights)
    {
        $this->placeholders['imageCopyrights'] = $copyrights;

        return $this;
    }

    public function placeholders(): array
    {
        return [
            'imageCopyrights' => array_map(function (Copyright $copyright) {
                return $copyright->toString();
            }, $this->placeholders['imageCopyrights']),
        ];
    }

    public function type(): string
    {
        return 'imprint';
    }

    public function metaTitleKey(): string
    {
        return 'global.imprint';
    }

    public function jigsawPathName(): string
    {
        return 'imprint';
    }
}
