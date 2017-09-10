<?php

namespace Ruvents\AbstractApiClient\Common;

trait ContextTrait
{
    /**
     * @var array
     */
    protected $context;

    /**
     * @return array
     */
    final public function getContext()
    {
        return $this->context;
    }
}
