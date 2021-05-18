<?php

namespace Twomedia\PoliciesBuilder\Policies;

use Twomedia\PoliciesBuilder\Contracts\CanBeBuiltInJigsaw;
use Twomedia\PoliciesBuilder\Contracts\Policy;
use Twomedia\PoliciesBuilder\Contracts\Stringable;

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
     * @param array<Stringable> $copyrights
     * @return $this
     */
    public function imageCopyrights(array $copyrights)
    {
        $this->placeholders['imageCopyrights'] = $copyrights;

        return $this;
    }

    public function domain(string $domain): self
    {
        $this->placeholders['domain'] = $domain;

        return $this;
    }

    public function placeholders(): array
    {
        $data = [];

        if (array_key_exists('imageCopyrights', $this->placeholders)) {
            $data['imageCopyrights'] = array_map(function (Stringable $copyright) {
                return (string) $copyright;
            }, $this->placeholders['imageCopyrights']);
        }

        if (array_key_exists('domain', $this->placeholders)) {
            $data['domain'] = $this->placeholders['domain'];
        }

        return $data;
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
