<?php

namespace Ruvents\AbstractApiClient\Common;

use Psr\Http\Message\ResponseInterface;

trait ContextResponseTrait
{
    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->getContext()['response'];
    }

    /**
     * @return array
     */
    abstract public function getContext();
}
