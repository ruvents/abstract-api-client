<?php

namespace Ruvents\AbstractApiClient\Event;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ruvents\AbstractApiClient\AbstractApiClient;

class PostSendEvent extends AbstractEvent
{
    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->context[AbstractApiClient::CONTEXT_REQUEST];
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->context[AbstractApiClient::CONTEXT_RESPONSE];
    }
}
