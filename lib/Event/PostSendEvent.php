<?php

namespace Ruvents\AbstractApiClient\Event;

use Ruvents\AbstractApiClient\Common\ContextRequestTrait;
use Ruvents\AbstractApiClient\Common\ContextResponseTrait;
use Ruvents\AbstractApiClient\Common\ContextTrait;
use Symfony\Component\EventDispatcher\Event;

class PostSendEvent extends Event
{
    use ContextTrait;
    use ContextRequestTrait;
    use ContextResponseTrait;

    public function __construct(array $context)
    {
        $this->context = $context;
    }
}
