<?php

namespace Ruvents\AbstractApiClient\Event;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\Event;

class PreSendEvent extends Event
{
    use ContextTrait, RequestTrait;

    /**
     * @var null|ResponseInterface
     */
    private $response;

    public function __construct(array $context, RequestInterface $request)
    {
        $this->context = $context;
        $this->request = $request;
    }

    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @return null|ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(ResponseInterface $response = null)
    {
        $this->response = $response;
    }
}
