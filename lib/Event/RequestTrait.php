<?php

namespace Ruvents\AbstractApiClient\Event;

use Psr\Http\Message\RequestInterface;

trait RequestTrait
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }
}
