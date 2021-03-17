<?php

namespace Twomedia\PoliciesBuilder\Contracts;

interface CanBeBuiltInJigsaw
{
    /**
     * The path name to a policy in Jigsaw.
     * String will be appended the the locale to be build.
     * Eg.
     * - `/de/{jigsawPathName}`
     * - `/en/{jigsawPathName}`
     *
     * @return string
     */
    public function jigsawPathName(): string;
}
