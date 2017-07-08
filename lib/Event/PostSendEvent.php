<?php

namespace Ruvents\AbstractApiClient\Event;

use Ruvents\AbstractApiClient\Common\ContextTrait;
use Symfony\Component\EventDispatcher\Event;

class PostSendEvent extends Event
{
    use ContextTrait;

    public function __construct(array $context)
    {
        $this->context = $context;
    }
}
