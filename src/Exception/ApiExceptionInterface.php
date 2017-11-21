<?php

namespace Ruvents\AbstractApiClient\Exception;

interface ApiExceptionInterface /** extends \Throwable */
{
    /**
     * @return array
     */
    public function getContext();
}
