<?php

namespace Ruvents\AbstractApiClient\Event;

use Ruvents\AbstractApiClient\Common;

class PostSendEvent extends AbstractEvent
{
    use Common\ContextRequestTrait;
    use Common\ContextResponseTrait;
}
