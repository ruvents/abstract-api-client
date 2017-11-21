<?php

namespace Ruvents\AbstractApiClient\Exception;

use Ruvents\AbstractApiClient\Common;

class DecodeException extends AbstractException
{
    use Common\ContextRequestTrait;
    use Common\ContextResponseTrait;
}
