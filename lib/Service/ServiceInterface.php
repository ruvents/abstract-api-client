<?php

namespace Ruvents\AbstractApiClient\Service;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ruvents\AbstractApiClient\ApiClientInterface;
use Ruvents\AbstractApiClient\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ServiceInterface extends EventSubscriberInterface
{
    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureDefaultContext(OptionsResolver $resolver);

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureRequestContext(OptionsResolver $resolver);

    /**
     * @param array              $context
     * @param ApiClientInterface $client
     *
     * @return RequestInterface
     */
    public function createRequest(array $context, ApiClientInterface $client);

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
     * @return void
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
     * @return void
     * @throws Exception\ApiException
     */
    public function validateData($data, array $context);
}
