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
                    $response = (new WebserviceClient())->getPolicyForPayload($this->getPayload($policy, $language));

                    $metaTitle = $this->metaTitle($language, $policy);

                    return $this->generateInMemoryJigsawPage($policy, $response, $language, $metaTitle);
                });
            })
            ->flatten(1);
    }

    /**
     * @return Collection
     */
    private function getLanguagesToGenerate(): Collection
    {
        return collect($this->policiesConfiguration['languages']);
    }

    /**
     * @return Collection
     */
    private function getPoliciesToGenerate(): Collection
    {
        return collect($this->policiesConfiguration['types']);
    }

    /**
     * @param Policy $type
     * @param string $language
     * @return array
     */
    private function getPayload(Policy $type, string $language): array
    {
        return (new CreatePayloadFromConfigurationAndPayload())->create(
            $this->policiesConfiguration,
            $type,
            $language
        );
    }

    /**
     * @param string $language
     * @param Policy $type
     * @return mixed
     */
    private function metaTitle(string $language, Policy $type)
    {
        return $this->translationFunction()($this->inMemoryPage($language), $type->metaTitleKey());
    }

    /**
     * @return Closure
     */
    private function translationFunction(): Closure
    {
        return $this->jigsawConfig->get('transGlobal');
    }

    /**
     * Build an anonymous class which acts as a "page" for the translation function
     *
     * @param string $lang
     * @return object
     */
    private function inMemoryPage(string $lang): object
    {
        return new class($lang) {
            public $lang;

            public function __construct($lang)
            {
                return $this->lang = $lang;
            }
        };
    }

    /**
     * @param Policy $type
     * @param Response $policy
     * @param string $language
     * @param mixed $metaTitle
     * @return array
     */
    private function generateInMemoryJigsawPage(Policy $type, Response $policy, string $language, string $metaTitle): array
    {
        $toJigsawPage = new PolicyToInMemoryJigsawPage();

        $html = $policy->json()['html'];

        return $toJigsawPage->generate($type, $html, $language, $metaTitle);
    }
}
