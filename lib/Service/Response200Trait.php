<?php

namespace Ruvents\AbstractApiClient\Service;

use Psr\Http\Message\ResponseInterface;
use Ruvents\AbstractApiClient\Exception\ResponseException;

trait Response200Trait
{
    /**
     * @see ServiceInterface::validateResponse()
     *
     * @param ResponseInterface $response
     * @param array             $context
     *
     * @throws ResponseException
     */
    public function validateResponse(ResponseInterface $response, array $context)
    {
        if (200 !== $code = $response->getStatusCode()) {
            throw new ResponseException($context, sprintf('Server responded with status code %d.', $code), $code);
        }
    }
}
