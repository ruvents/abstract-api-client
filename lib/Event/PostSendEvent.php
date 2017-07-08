<?php

namespace Ruvents\AbstractApiClient\Event;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\Event;

class PostSendEvent extends Event
{
    use ContextTrait, RequestTrait;

    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(array $context, RequestInterface $request, ResponseInterface $response)
    {
        $this->context = $context;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}
