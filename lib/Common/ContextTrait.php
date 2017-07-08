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
    public function getContext()
    {
        return $this->context;
    }
}
