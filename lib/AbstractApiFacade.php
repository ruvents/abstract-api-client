<?php

namespace Ruvents\AbstractApiClient;

use Http\Message\RequestFactory;

abstract class AbstractApiFacade
{
    /**
     * @var AbstractApiClient
     */
    protected $client;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    public function __construct(AbstractApiClient $client, RequestFactory $requestFactory)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @return string
     */
    final public static function getClass()
    {
        return get_called_class();
    }

    protected function requestGet($endpoint, array $params, array $context, array $headers = [])
    {
        $request = $this->requestFactory->createRequest('GET', $endpoint.'?'.http_build_query($params), $headers);

        return $this->client->request($request, $context);
    }

    protected function requestPost($endpoint, array $params, array $context, array $headers = [])
    {
        $request = $this->requestFactory->createRequest('POST', $endpoint, $headers, http_build_query($params));

        return $this->client->request($request, $context);
    }
}