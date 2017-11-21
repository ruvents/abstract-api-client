<?php

namespace Ruvents\AbstractApiClient\Event;

use Psr\Http\Message\RequestInterface;
use Ruvents\AbstractApiClient\AbstractApiClient;

class PreSendEvent extends AbstractEvent
{
    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->context[AbstractApiClient::CONTEXT_REQUEST];
    }

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
