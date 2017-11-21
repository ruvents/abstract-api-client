<?php

namespace Ruvents\AbstractApiClient\Exception;

use Psr\Http\Message\ResponseInterface;
use Throwable;

class ResponseException extends \RuntimeException implements ApiExceptionInterface
{
    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(ResponseInterface $response, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}
