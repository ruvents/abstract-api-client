<?php

namespace Ruvents\AbstractApiClient\Common;

trait ContextTrait
{
    /**
     * @var array
     */
    private $context;

    /**
     * @return array
     */
    final public function getContext()
    {
        return $this->context;
    }
}
