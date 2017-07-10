<?php

namespace Ruvents\AbstractApiClient\Common;

trait ContextDataTrait
{
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->getContext()['data'];
    }

    /**
     * @return array
     */
    abstract public function getContext();
}
