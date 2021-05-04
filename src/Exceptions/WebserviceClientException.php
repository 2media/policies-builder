<?php

namespace Twomedia\PoliciesBuilder\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class WebserviceClientException extends Exception
{
    public static function policyNotFoundException(array $payload, Response $response): self
    {
        $jsonPayload = json_encode($payload);

        return new static("Could not find matching policy for given payload: $jsonPayload}. (Response: {$response->body()})");
    }
}
