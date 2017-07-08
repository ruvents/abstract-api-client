<?php

namespace Ruvents\AbstractApiClient\Event;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ruvents\AbstractApiClient\Common\ContextTrait;
use Symfony\Component\EventDispatcher\Event;

class PostDecodeEvent extends Event
{
    use ContextTrait;

    /**
     * @var mixed
     */
    private $data;

    public function __construct(array $context, $data)
    {
        $this->context = $context;
        $this->data = $data;
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

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
