<?php

namespace Ruvents\AbstractApiClient\Event;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ruvents\AbstractApiClient\Common\ContextTrait;
use Symfony\Component\EventDispatcher\Event;

class PostSendEvent extends Event
{
    use ContextTrait;

    public function __construct(array $context)
    {
        $this->context = $context;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->context['_request'];
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->context['_response'];
    }
}
