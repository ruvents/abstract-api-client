<?php

namespace Ruvents\AbstractApiClient\Exception;

use Ruvents\AbstractApiClient\Common;

class DecodeException extends ApiException
{
    use Common\ContextRequestTrait;
    use Common\ContextResponseTrait;
}
