<?php

namespace Twomedia\PoliciesBuilder\Tests\Cms\Jigsaw;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\Cms\Jigsaw\PoliciesCollection;
use Twomedia\PoliciesBuilder\DTOs\Copyright;
use Twomedia\PoliciesBuilder\Policies\Imprint;
use Twomedia\PoliciesBuilder\Policies\TermsOfService;
use Twomedia\PoliciesBuilder\PoliciesConfiguration;
use Twomedia\PoliciesBuilder\Translations\GlobalTranslator;

class PoliciesCollectionTest extends TestCase
{
    /** @test */
    public function returns_array_for_each_defined_policy_and_language()
    {
        $config = $this->getJigsawConfiguration();

        $result = (new PoliciesCollection())->generate($config);

        $this->assertCount(4, $result);

        // TODO: Assert that content looks as expected
    }

    protected function getJigsawConfiguration(): Collection
    {
        return collect([
            'transGlobal' => function ($page, $key, array $replace = []) {
                return GlobalTranslator::trans($page, $key, $replace);
            },

            'policies' => PoliciesConfiguration::make()
                ->languages(['de', 'fr'])
                ->domain('onlineanfrage.ch')
                ->brand('2media')
                ->types([
                    TermsOfService::make(),
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
