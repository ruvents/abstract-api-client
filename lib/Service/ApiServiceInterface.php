<?php

namespace Ruvents\AbstractApiClient\Service;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ruvents\AbstractApiClient\Exception;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ApiServiceInterface
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureContext(OptionsResolver $resolver);

    /**
     * @param array $context
     *
     * @return RequestInterface
     */
    public function createRequest(array $context);

    /**
     * @param RequestInterface $request
     * @param array            $context
     *
     * @return ResponseInterface
     * @throws Exception\RequestException
     */
    public function sendRequest(RequestInterface $request, array $context);

    /**
     * @param ResponseInterface $response
     * @param array             $context
     *
     * @throws Exception\ResponseException
     */
    public function validateResponse(ResponseInterface $response, array $context);

    /**
     * @param ResponseInterface $response
     * @param array             $context
     *
     * @return mixed
     * @throws Exception\DecodeException
     */
    public function decodeResponse(ResponseInterface $response, array $context);

    /**
     * @param mixed $data
     * @param array $context
     *
     * @throws Exception\ApiException
     */
    public function validateData($data, array $context);
}
