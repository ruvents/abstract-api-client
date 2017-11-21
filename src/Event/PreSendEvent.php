<?php

namespace Ruvents\AbstractApiClient\Event;

use Psr\Http\Message\RequestInterface;
use Ruvents\AbstractApiClient\AbstractApiClient;
use Ruvents\AbstractApiClient\Common;

class PreSendEvent extends AbstractEvent
{
    use Common\ContextRequestTrait;

    public function setRequest(RequestInterface $request)
    {
        $this->context[AbstractApiClient::CONTEXT_REQUEST] = $request;
    }

    public function setResponseData($responseData)
    {
        $this->context[AbstractApiClient::CONTEXT_RESPONSE_DATA] = $responseData;
        $this->stopPropagation();
    }
}
