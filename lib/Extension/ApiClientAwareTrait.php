<?php

namespace Ruvents\AbstractApiClient\Extension;

use Ruvents\AbstractApiClient\ApiClientInterface;

trait ApiClientAwareTrait
{
    /**
     * @var ApiClientInterface
     */
    protected $apiClient;

    /**
     * @see ApiClientAwareInterface::setApiClient()
     *
     * @param ApiClientInterface $apiClient
     */
    public function setApiClient(ApiClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }
}
