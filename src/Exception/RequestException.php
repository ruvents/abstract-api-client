<?php

namespace Ruvents\AbstractApiClient\Exception;

use Psr\Http\Message\RequestInterface;
use Throwable;

class RequestException extends \RuntimeException implements ApiExceptionInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(RequestInterface $request, $message = '', $code = 0, Throwable $previous = null)
    {
        $this->request = $request;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }
}
