<?php

namespace Ruvents\AbstractApiClient\Exception;

use Ruvents\AbstractApiClient\Common\ContextRequestTrait;

class RequestException extends AbstractException
{
    use ContextRequestTrait;
}
