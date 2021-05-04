<?php

namespace Twomedia\PoliciesBuilder\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Twomedia\PoliciesBuilder\Exceptions\WebserviceClientException;

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

        if ($response->status() === 404) {
            throw WebserviceClientException::policyNotFoundException($payload, $response);
        }

        $response->throw();

        return $response;
    }
}
