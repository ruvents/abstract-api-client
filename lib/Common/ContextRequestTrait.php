<?php

namespace Ruvents\AbstractApiClient\Common;

use Psr\Http\Message\RequestInterface;

trait ContextRequestTrait
{
    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->getContext()['_request'];
    }

    /**
     * @return array
     */
    abstract public function getContext();
}
