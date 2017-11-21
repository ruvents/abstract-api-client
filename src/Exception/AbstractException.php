<?php

namespace Ruvents\AbstractApiClient\Exception;

use Ruvents\AbstractApiClient\Common\ContextTrait;

abstract class AbstractException extends \RuntimeException implements ApiExceptionInterface
{
    use ContextTrait;

    public function __construct(array $context, $message = '', $code = 0, \Exception $previous = null)
    {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }
}
