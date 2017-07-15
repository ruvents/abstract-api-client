<?php

namespace Ruvents\AbstractApiClient\Extension;

use Ruvents\AbstractApiClient\ApiClientInterface;

interface ApiClientAwareInterface
{
    /**
     * @param ApiClientInterface $apiClient
     */
    public function setApiClient(ApiClientInterface $apiClient);
}
