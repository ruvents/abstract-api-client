<?php

namespace Ruvents\AbstractApiClient\Common;

use Psr\Http\Message\ResponseInterface;
use Ruvents\AbstractApiClient\AbstractApiClient;

trait ContextResponseTrait
{
    /**
     * @return ResponseInterface
     */
    final public function getResponse()
    {
        return $this->getContext()[AbstractApiClient::CONTEXT_RESPONSE];
    }

    /**
     * @return array
     */
    abstract public function getContext();
}
