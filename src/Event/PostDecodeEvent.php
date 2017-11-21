<?php

namespace Ruvents\AbstractApiClient\Event;

use Ruvents\AbstractApiClient\AbstractApiClient;
use Ruvents\AbstractApiClient\Common;

class PostDecodeEvent extends AbstractEvent
{
    use Common\ContextRequestTrait;
    use Common\ContextResponseTrait;
    use Common\ContextResponseDataTrait;

    /**
     * @param mixed $responseData
     */
    public function setResponseData($responseData)
    {
        $this->context[AbstractApiClient::CONTEXT_RESPONSE_DATA] = $responseData;
    }
}
