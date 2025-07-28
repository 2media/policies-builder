<?php

namespace Twomedia\PoliciesBuilder\Contracts;

interface CanBeBuiltInJigsaw
{
    /**
     * The path name to a policy in Jigsaw.
     * String will be appended the locale to be built.
     * E.g.
     * - `/de/{jigsawPathName}`
     * - `/en/{jigsawPathName}`
     */
    public function jigsawPathName(): string;
}
