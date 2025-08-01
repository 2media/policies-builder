<?php

namespace Twomedia\PoliciesBuilder\Tests\Cms\Jigsaw;

use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\Cms\Jigsaw\PoliciesCollection;
use Twomedia\PoliciesBuilder\DTOs\CooperationPartner;
use Twomedia\PoliciesBuilder\DTOs\Copyright;
use Twomedia\PoliciesBuilder\DTOs\IconCopyright;
use Twomedia\PoliciesBuilder\Policies\Imprint;
use Twomedia\PoliciesBuilder\Policies\TermsOfService;
use Twomedia\PoliciesBuilder\PoliciesConfiguration;
use Twomedia\PoliciesBuilder\Translations\GlobalTranslator;

class PoliciesCollectionTest extends TestCase
{
    public Container $container;

    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container;

        $container['config'] = collect([
            'cache.default' => 'file',
            'cache.stores.file' => [
                'driver' => 'file',
                'path' => __DIR__.'/../../../storage/cache',
            ],
        ]);
        $container['files'] = new Filesystem;

        $cacheManager = new CacheManager($container);

        // Register CacheManager in Container
        $container->singleton(CacheManager::class, fn () => $cacheManager);

        $this->container = $container;
    }

    /** @test */
    public function returns_array_for_each_defined_policy_and_language()
    {
        $config = $this->getJigsawConfiguration();

        $result = (new PoliciesCollection)->generate($config);

        $this->assertCount(4, $result);

        $termsOfServicePolicies = $result->filter(fn ($policy) => $policy['policy_type'] === 'terms');

        $this->assertCount(2, $termsOfServicePolicies);
        $this->assertEquals('Nutzungsbedingungen', $termsOfServicePolicies[0]['meta_title']);
        $this->assertEquals("Conditions d'utilisation", $termsOfServicePolicies[2]['meta_title']);
        $this->assertEquals('', $termsOfServicePolicies[0]['meta_description']);
        $this->assertEquals('', $termsOfServicePolicies[2]['meta_description']);

        $imprintPolicies = $result->filter(fn ($policy) => $policy['policy_type'] === 'imprint');

        $this->assertCount(2, $imprintPolicies);
        $this->assertEquals('Impressum', $imprintPolicies[1]['meta_title']);
        $this->assertEquals('Mentions légales', $imprintPolicies[3]['meta_title']);
        $this->assertEquals('', $imprintPolicies[1]['meta_description']);
        $this->assertEquals('', $imprintPolicies[3]['meta_description']);
    }

    protected function getJigsawConfiguration(): Collection
    {
        return collect([
            'transGlobal' => fn ($page, $key, array $replace = []) => $this->container->make(GlobalTranslator::class)->trans($page, $key, $replace),

            'policies' => PoliciesConfiguration::make()
                ->languages(['de', 'fr'])
                ->domain('onlineanfrage.ch')
                ->brand('2media')
                ->variant('services')
                ->types([
                    TermsOfService::make()
                        ->inCooperationWith(CooperationPartner::make(
                            'Swisscom Directories AG',
                            'Swisscom Directories',
                            'https://www.renovero.ch/'
                        )),
                    Imprint::make()
                        ->imageCopyrights([
                            Copyright::make('2mmedia', 'Adobe Stock', 'Klavierumzug'),
                            Copyright::make('adam121', 'Envato Elements', 'Handwerker Startseite'),
                            Copyright::make('bialasiewicz', 'Envato Elements', 'Maler, Garagentor'),
                            Copyright::make('duallogic', 'Envato Elements', 'Handwerker, Elektriker, Heizung, Dachdecker, Gärtner, Parkett'),
                            Copyright::make('ivankmit', 'Envato Elements', 'Bodenleger'),
                            Copyright::make('klenova', 'Envato Elements', 'Reinigung'),
                            Copyright::make('leikapro', 'Envato Elements', 'Umzug'),
                            Copyright::make('mariesacha', 'Adobe Stock', 'Katzentürchen'),
                            Copyright::make('seventyfourimages', 'Envato Elements', 'Schreiner'),
                            Copyright::make('Vladdeep', 'Envato Elements', 'Sanitär'),
                            Copyright::make('Wavebreak Media Ltd', 'Bigstockphoto.com', 'Fenster'),
                            IconCopyright::make('thenounproject.com'),
                        ]),

                    // These Policies are not implemented yet and the API Requests will fail!
                    // PrivacyPolicy::make(),
                    // ->usesSnapchatPixel()
                    // ->usesFacebookPixel()
                    // ->usesGoogleExperiments()
                    // ->usesTypeForm()
                    // ->usesGoogleAnalytics()
                    // ConditionsOfParticipations::make(),
                    // ->endDate('30.07.2021')
                ]),
        ]);
    }
}
