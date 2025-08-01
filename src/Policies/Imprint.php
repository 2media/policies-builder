<?php

namespace Twomedia\PoliciesBuilder\Policies;

use Twomedia\PoliciesBuilder\Contracts\CanBeBuiltInJigsaw;
use Twomedia\PoliciesBuilder\Contracts\Policy;
use Twomedia\PoliciesBuilder\Contracts\Stringable;

class Imprint implements CanBeBuiltInJigsaw, Policy
{
    public array $placeholders = [
        'imageCopyrights' => [],
    ];

    public static function make(): self
    {
        return new self;
    }

    /**
     * @param  array<Stringable>  $copyrights
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
            'imageCopyrights' => array_map(fn (Stringable $copyright) => (string) $copyright, $this->placeholders['imageCopyrights']),
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
