<?php

namespace Ruvents\AbstractApiClient\Exception;

use Ruvents\AbstractApiClient\Common;

class ResponseException extends ApiException
{
    use Common\ContextRequestTrait;
    use Common\ContextResponseTrait;
}
