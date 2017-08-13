<?php

namespace Ruvents\AbstractApiClient\Common;

use Ruvents\AbstractApiClient\AbstractApiClient;
use Ruvents\AbstractApiClient\ApiClientInterface;

trait ContextTrait
{
    /**
     * @var array
     */
    private $context;

    /**
     * @return array
     */
    final public function getContext()
    {
        return $this->context;
    }

    /**
     * @return ApiClientInterface
     */
    final public function getApiClient()
    {
        return $this->context[AbstractApiClient::CONTEXT_API_CLIENT];
    }
}
