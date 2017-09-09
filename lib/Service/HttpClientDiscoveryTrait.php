<?php

namespace Ruvents\AbstractApiClient\Service;

use Http\Client\Exception;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ruvents\AbstractApiClient\Exception\RequestException;

trait HttpClientDiscoveryTrait
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @see ApiServiceInterface::sendRequest()
     *
     * @param RequestInterface $request
     * @param array            $context
     *
     * @return ResponseInterface
     * @throws RequestException
     */
    public function sendRequest(RequestInterface $request, array $context)
    {
        if (!isset($this->httpClient)) {
            $this->httpClient = HttpClientDiscovery::find();
        }

        try {
            return $this->httpClient->sendRequest($request);
        } catch (Exception $exception) {
            throw new RequestException($context, 'Failed to process request', 0, $exception);
        }
    }
}
