<?php

namespace Twomedia\PoliciesBuilder;

use Twomedia\PoliciesBuilder\Contracts\Policy;

class CreatePayloadFromConfigurationAndPayload
{
    public function create(PoliciesConfiguration $configuration, Policy $policy, string $language): array
    {
        return [
            'brand' => $configuration['brand'],
            'variant' => $configuration['variant'],

            'type' => $policy->type(),
            'lang' => $language,

            'placeholders' => array_merge(
                $this->defaultPlaceholders($configuration),
                $policy->placeholders(),
            ),
        ];
    }

    private function defaultPlaceholders(PoliciesConfiguration $configuration): array
    {
        return [
            'domain' => $configuration['domain'],
        ];
    }
}
