<?php

namespace Ruvents\AbstractApiClient\Event;

use Psr\Http\Message\RequestInterface;
use Ruvents\AbstractApiClient\Common;
use Symfony\Component\EventDispatcher\Event;

class PreSendEvent extends Event
{
    use Common\ContextTrait;
    use Common\ContextRequestTrait;

    public function __construct(array $context)
    {
        $this->context = $context;
    }

    public function setRequest(RequestInterface $request)
    {
        $this->context['request'] = $request;
    }

    public function setData($data)
    {
        $this->context['data'] = $data;

        $this->stopPropagation();
    }
}
