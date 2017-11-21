<?php

namespace Ruvents\AbstractApiClient\Definition;

use Psr\Http\Message\ResponseInterface;
use Ruvents\AbstractApiClient\Exception\DecodeException;

trait JsonDecodeTrait
{
    /**
     * @see ApiDefinitionInterface::decodeResponse()
     *
     * @param ResponseInterface $response
     * @param array             $context
     *
     * @return mixed
     * @throws DecodeException
     */
    public function decodeResponse(ResponseInterface $response, array $context)
    {
        $decoded = json_decode((string)$response->getBody(), true);

        if (JSON_ERROR_NONE !== $code = json_last_error()) {
            throw new DecodeException(json_last_error_msg(), $code);
        }

        return $decoded;
    }
}
