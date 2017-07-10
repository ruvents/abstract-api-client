<?php

namespace Ruvents\AbstractApiClient\Exception;

use Ruvents\AbstractApiClient\Common;

class ResponseException extends ErrorEventException
{
    use Common\ContextRequestTrait;
    use Common\ContextResponseTrait;
}
