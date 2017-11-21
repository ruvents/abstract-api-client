<?php

namespace Ruvents\AbstractApiClient\Definition;

use Http\Client\Exception;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ruvents\AbstractApiClient\Exception\RequestException;

trait HttpClientDiscoveryTrait
{
    /**
     * @var null|HttpClient
     */
    protected $httpClient;

    /**
     * @see ApiDefinitionInterface::sendRequest()
     *
     * @param RequestInterface $request
     * @param array            $context
     *
     * @return ResponseInterface
     * @throws RequestException
     */
    public function sendRequest(RequestInterface $request, array $context)
    {
        try {
            return $this->getHttpClient()->sendRequest($request);
        } catch (Exception $exception) {
            throw new RequestException($context, 'Failed to process request', 0, $exception);
        }
    }

    /**
     * @return HttpClient
     */
    protected function getHttpClient()
    {
        if (null === $this->httpClient) {
            $this->httpClient = HttpClientDiscovery::find();
        }

        return $this->httpClient;
    }
}
