<?php

namespace Ruvents\AbstractApiClient\Common;

use Psr\Http\Message\RequestInterface;
use Ruvents\AbstractApiClient\AbstractApiClient;

trait ContextRequestTrait
{
    /**
     * @return RequestInterface
     */
    final public function getRequest()
    {
        return $this->getContext()[AbstractApiClient::CONTEXT_REQUEST];
    }

    /**
     * @return array
     */
    abstract public function getContext();
}
