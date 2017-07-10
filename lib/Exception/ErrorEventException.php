<?php

namespace Ruvents\AbstractApiClient\Exception;

use Ruvents\AbstractApiClient\Common\ContextTrait;

class ErrorEventException extends \RuntimeException
{
    use ContextTrait;

    public function __construct(array $context, $message = '', $code = 0, \Exception $previous = null)
    {
        $this->context = $context;
        parent::__construct($message, 0, $previous);
    }
}
