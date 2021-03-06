<?php

namespace Ruvents\AbstractApiClient\Definition;

use Http\Message\RequestFactory;
use Http\Message\UriFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ruvents\AbstractApiClient\ApiClientInterface;
use Ruvents\AbstractApiClient\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ApiDefinitionInterface extends EventSubscriberInterface
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
     * @param RequestFactory     $requestFactory
     * @param UriFactory         $uriFactory
     * @param array              $context
     * @param ApiClientInterface $client
     *
     * @return RequestInterface
     */
    public function createRequest(UriFactory $uriFactory, RequestFactory $requestFactory, ApiClientInterface $client, array $context);

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
     * @throws Exception\ServiceException
     */
    public function validateData($data, array $context);
}
