<?php

namespace Ruvents\AbstractApiClient\Event;

use Ruvents\AbstractApiClient\Common;
use Symfony\Component\EventDispatcher\Event;

class PostSendEvent extends Event
{
    use Common\ContextTrait;
    use Common\ContextRequestTrait;
    use Common\ContextResponseTrait;

    public function __construct(array $context)
    {
        $this->context = $context;
    }
}
