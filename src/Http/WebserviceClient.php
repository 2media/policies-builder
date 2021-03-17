<?php

namespace Twomedia\PoliciesBuilder\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

class WebserviceClient
{
    // TODO: Update Endpoint to Production API
    const API_ENDPOINT = 'http://127.0.0.1:8000/policies?';

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
