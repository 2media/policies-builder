<?php

namespace Twomedia\PoliciesBuilder\Contracts;

use Twomedia\PoliciesBuilder\DTOs\ResolvedPolicy;
use Twomedia\PoliciesBuilder\PoliciesConfiguration;

interface PolicySource
{
    public function resolve(Policy $policy, string $language, PoliciesConfiguration $configuration): ResolvedPolicy;
}
