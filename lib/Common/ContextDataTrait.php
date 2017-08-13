<?php

namespace Ruvents\AbstractApiClient\Common;

use Ruvents\AbstractApiClient\AbstractApiClient;

trait ContextDataTrait
{
    /**
     * @return mixed
     */
    final public function getData()
    {
        return $this->getContext()[AbstractApiClient::CONTEXT_DATA];
    }

    /**
     * @return array
     */
    abstract public function getContext();
}
