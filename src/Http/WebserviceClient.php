<?php

namespace Twomedia\PoliciesBuilder\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

class WebserviceClient
{
    const API_ENDPOINT = 'https://v2.webservice.apy.ch/policies?';

    /**
     * @param array $payload
     * @return Response
     * @throws RequestException
     */
    public function getPolicyForPayload(array $payload): Response
    {
        $url = self::API_ENDPOINT . http_build_query($payload);

        $response = (new PendingRequest())->get($url);

        $response->throw();

        return $response;
    }
}
