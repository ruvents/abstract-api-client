<?php

namespace Ruvents\AbstractApiClient\Definition;

use Psr\Http\Message\ResponseInterface;
use Ruvents\AbstractApiClient\Exception\ResponseException;

trait Response200Trait
{
    /**
     * @see ApiDefinitionInterface::validateResponse()
     *
     * @param ResponseInterface $response
     * @param array             $context
     *
     * @throws ResponseException
     */
    public function validateResponse(ResponseInterface $response, array $context)
    {
        if (200 !== $code = $response->getStatusCode()) {
            throw new ResponseException($response, sprintf('Server responded with status code %d.', $code));
        }
    }
}
