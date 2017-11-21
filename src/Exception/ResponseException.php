<?php

namespace Ruvents\AbstractApiClient\Exception;

use Ruvents\AbstractApiClient\Common;

class ResponseException extends AbstractException
{
    use Common\ContextRequestTrait;
    use Common\ContextResponseTrait;
}
