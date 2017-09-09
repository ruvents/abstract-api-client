<?php

namespace Ruvents\AbstractApiClient\Exception;

use Ruvents\AbstractApiClient\Common\ContextRequestTrait;

class RequestException extends ApiException
{
    use ContextRequestTrait;
}
