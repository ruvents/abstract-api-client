<?php

namespace Ruvents\AbstractApiClient\Exception;

use Ruvents\AbstractApiClient\Common;

class DecodeException extends ErrorEventException
{
    use Common\ContextRequestTrait;
    use Common\ContextResponseTrait;
}
