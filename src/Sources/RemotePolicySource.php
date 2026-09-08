<?php

namespace Twomedia\PoliciesBuilder\Sources;

use Closure;
use Twomedia\PoliciesBuilder\Cms\Jigsaw\FakePage;
use Twomedia\PoliciesBuilder\Contracts\Policy;
use Twomedia\PoliciesBuilder\Contracts\PolicySource;
use Twomedia\PoliciesBuilder\CreatePayloadFromConfigurationAndPayload;
use Twomedia\PoliciesBuilder\DTOs\ResolvedPolicy;
use Twomedia\PoliciesBuilder\Http\WebserviceClient;
use Twomedia\PoliciesBuilder\PoliciesConfiguration;

/**
 * Resolves a Policy by calling the remote policies webservice.
 *
 * This is the historic (pre-snapshot) behaviour: every `resolve()` call
 * performs a live HTTP request. It is kept around so it can be used by the
 * one-off snapshot generation command, and as a fallback for projects that
 * haven't opted into `PoliciesConfiguration::snapshotPath()` yet.
 */
class RemotePolicySource implements PolicySource
{
    public function __construct(private readonly ?Closure $translationFunction = null) {}

    public function resolve(Policy $policy, string $language, PoliciesConfiguration $configuration): ResolvedPolicy
    {
        $payload = (new CreatePayloadFromConfigurationAndPayload)->create($configuration, $policy, $language);

        $response = (new WebserviceClient)->getPolicyForPayload($payload);

        $html = $response->json()['html'];

        return new ResolvedPolicy(
            policyType: method_exists($policy, 'jigsawPathName') ? $policy->jigsawPathName() : $policy->type(),
            locale: $language,
            metaTitle: $this->resolveMetaTitle($language, $policy),
            metaDescription: '',
            content: $html,
        );
    }

    private function resolveMetaTitle(string $language, Policy $policy): string
    {
        if ($this->translationFunction === null) {
            return '';
        }

        $page = new FakePage($language);

        return ($this->translationFunction)($page, $policy->metaTitleKey());
    }
}
