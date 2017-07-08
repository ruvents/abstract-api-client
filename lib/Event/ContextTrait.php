<?php

namespace Ruvents\AbstractApiClient\Event;

trait ContextTrait
{
    /**
     * @var array
     */
    private $context;

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }
}
