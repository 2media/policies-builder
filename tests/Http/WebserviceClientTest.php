<?php

namespace Twomedia\PoliciesBuilder\Tests\Http;

use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;
use Twomedia\PoliciesBuilder\Exceptions\WebserviceClientException;
use Twomedia\PoliciesBuilder\Http\WebserviceClient;

class WebserviceClientTest extends TestCase
{
    /** @test */
    public function it_makes_request_to_webservice_for_given_payload()
    {
        $payload = [
            'brand' => '2media',
            'variant' => 'default',
            'type' => 'imprint',
            'lang' => 'de',
            'placeholders' => [
                'domain' => '2media.ch',
            ],
        ];

        $client = new WebserviceClient();

        $response = $client->getPolicyForPayload($payload);

        $this->assertInstanceOf(Response::class, $response);

        $this->assertArrayHasKey('html', $response->json());
    }

    /** @test */
    public function it_throws_custom_http_exception_if_request_fails()
    {
        $this->expectException(WebserviceClientException::class);

        $payload = [
            'brand' => '::brand-does-not-exist::',
            'variant' => 'default',
            'type' => 'imprint',
            'lang' => 'de',
            'placeholders' => [
                'domain' => '2media.ch',
            ],
        ];

        $client = new WebserviceClient();

        $response = $client->getPolicyForPayload($payload);
    }
}
