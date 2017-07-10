<?php

namespace Ruvents\AbstractApiClient\Exception;

use Ruvents\AbstractApiClient\Common;

class ApiException extends ErrorEventException
{
    use Common\ContextRequestTrait;
    use Common\ContextResponseTrait;
    use Common\ContextDataTrait;
}
