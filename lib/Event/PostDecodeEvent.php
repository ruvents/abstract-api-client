<?php

namespace Ruvents\AbstractApiClient\Event;

use Ruvents\AbstractApiClient\AbstractApiClient;
use Ruvents\AbstractApiClient\Common;
use Symfony\Component\EventDispatcher\Event;

class PostDecodeEvent extends Event
{
    use Common\ContextTrait;
    use Common\ContextRequestTrait;
    use Common\ContextResponseTrait;
    use Common\ContextDataTrait;

    public function __construct(array $context)
    {
        $this->context = $context;
    }

    /**
     * @param mixed $responseData
     */
    public function setResponseData($responseData)
    {
        $this->context[AbstractApiClient::CONTEXT_RESPONSE_DATA] = $responseData;
    }
}
