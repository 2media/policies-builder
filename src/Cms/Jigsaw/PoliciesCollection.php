<?php

namespace Twomedia\PoliciesBuilder\Cms\Jigsaw;

use Closure;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Twomedia\PoliciesBuilder\Contracts\Policy;
use Twomedia\PoliciesBuilder\CreatePayloadFromConfigurationAndPayload;
use Twomedia\PoliciesBuilder\Http\WebserviceClient;
use Twomedia\PoliciesBuilder\PoliciesConfiguration;

class PoliciesCollection
{
    private Collection $jigsawConfig;

    private PoliciesConfiguration $policiesConfiguration;

    public function generate(Collection $config): Collection
    {
        $this->jigsawConfig = $config;
        $this->policiesConfiguration = $config->get('policies');

        return $this->getLanguagesToGenerate()
            ->map(function (string $language) {
                return $this->getPoliciesToGenerate()->map(function (Policy $policy) use ($language) {
                    $response = (new WebserviceClient)->getPolicyForPayload($this->getPayload($policy, $language));

                    $metaTitle = $this->getTranslatedMetaTitle($language, $policy);

                    return $this->generateInMemoryJigsawPage($policy, $response, $language, $metaTitle);
                });
            })
            ->flatten(1);
    }

    private function getLanguagesToGenerate(): Collection
    {
        return collect($this->policiesConfiguration['languages']);
    }

    private function getPoliciesToGenerate(): Collection
    {
        return collect($this->policiesConfiguration['types']);
    }

    private function getPayload(Policy $type, string $language): array
    {
        return (new CreatePayloadFromConfigurationAndPayload)->create(
            $this->policiesConfiguration,
            $type,
            $language
        );
    }

    /**
     * @return mixed
     */
    private function getTranslatedMetaTitle(string $language, Policy $type)
    {
        $page = new FakePage($language);

        return $this->translationFunction()($page, $type->metaTitleKey());
    }

    private function translationFunction(): ?Closure
    {
        return $this->jigsawConfig->get('transGlobal');
    }

    /**
     * @param  mixed  $metaTitle
     */
    private function generateInMemoryJigsawPage(Policy $type, Response $policy, string $language, string $metaTitle): array
    {
        $toJigsawPage = new PolicyToInMemoryJigsawPage;

        $html = $policy->json()['html'];

        /** @psalm-suppress InvalidArgument */
        return $toJigsawPage->generate($type, $html, $language, $metaTitle);
    }
}
