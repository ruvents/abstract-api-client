<?php

namespace Ruvents\AbstractApiClient\Common;

use Ruvents\AbstractApiClient\AbstractApiClient;

trait ContextResponseDataTrait
{
    /**
     * @return mixed
     */
    final public function getResponseData()
    {
        return $this->getContext()[AbstractApiClient::CONTEXT_RESPONSE_DATA];
    }

    /**
     * @return array
     */
    abstract public function getContext();
}
