<?php

namespace Ruvents\AbstractApiClient\Service;

use Psr\Http\Message\ResponseInterface;
use Ruvents\AbstractApiClient\Exception\DecodeException;

trait JsonDecodeTrait
{
    /**
     * @see ApiServiceInterface::decodeResponse()
     *
     * @param ResponseInterface $response
     * @param array             $context
     *
     * @return mixed
     * @throws DecodeException
     */
    public function decodeResponse(ResponseInterface $response, array $context)
    {
        $decoded = $this->jsonDecode((string)$response->getBody());

        if (JSON_ERROR_NONE !== $code = json_last_error()) {
            throw new DecodeException($context, json_last_error_msg(), $code);
        }

        return $decoded;
    }

    /**
     * @param string $string
     * @param bool   $associative
     * @param int    $depth
     * @param int    $options
     *
     * @return mixed
     */
    protected function jsonDecode($string, $associative = true, $depth = 512, $options = 0)
    {
        return json_decode($string, $associative, $depth, $options);
    }
}
