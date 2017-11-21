<?php

namespace Ruvents\AbstractApiClient\Exception;

use Ruvents\AbstractApiClient\Common;

class ServiceException extends AbstractException
{
    use Common\ContextRequestTrait;
    use Common\ContextResponseTrait;
}
