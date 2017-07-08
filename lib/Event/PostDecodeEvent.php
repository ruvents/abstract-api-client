<?php

namespace Ruvents\AbstractApiClient\Event;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\Event;

class PostDecodeEvent extends Event
{
    use ContextTrait, RequestTrait;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var mixed
     */
    private $data;

    public function __construct(array $context, RequestInterface $request, ResponseInterface $response, $data)
    {
        $this->context = $context;
        $this->request = $request;
        $this->response = $response;
        $this->data = $data;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}
