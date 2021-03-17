<?php

namespace Twomedia\PoliciesBuilder\Tests\Http;

use Illuminate\Http\Client\Response;
use Twomedia\PoliciesBuilder\Http\WebserviceClient;
use PHPUnit\Framework\TestCase;

class WebserviceClientTest extends TestCase
{
    /** @test */
    public function it_makes_request_to_webservice_for_given_payload()
    {
        // it makes request to webservice for given payload
        $payload = [
            'brand' => '2media',
            'variant' => 'default',
            'type' => 'imprint',
            'lang' => 'de',
            'placeholders' => [
                'domain' => '2media.ch'
            ]
        ];

        $client = new WebserviceClient();

        $response = $client->getPolicyForPayload($payload);

        $this->assertInstanceOf(Response::class, $response);

        $this->assertArrayHasKey('html', $response->json());
    }
}
