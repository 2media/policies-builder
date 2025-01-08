<?php

namespace Twomedia\PoliciesBuilder;

use LogicException;
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
        if (! array_key_exists('domain', $configuration->toArray())) {
            throw new LogicException('Domain is missing. Define one by using domain() on the PoliciesConfiguration.');
        }

        return [
            'domain' => $configuration['domain'],
        ];
    }
}
