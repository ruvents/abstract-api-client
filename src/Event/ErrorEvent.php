<?php

namespace Ruvents\AbstractApiClient\Event;

use Ruvents\AbstractApiClient\ApiClientInterface;
use Ruvents\AbstractApiClient\Exception\ApiExceptionInterface;

class ErrorEvent extends AbstractEvent
{
    /**
     * @var ApiExceptionInterface
     */
    private $exception;

    /**
     * @var mixed
     */
    private $validData;

    public function __construct(ApiExceptionInterface $exception, ApiClientInterface $client, array $context)
    {
        parent::__construct($client, $context);
        $this->setException($exception);
    }

    /**
     * @return ApiExceptionInterface
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param ApiExceptionInterface $exception
     */
    public function setException(ApiExceptionInterface $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return mixed
     */
    public function getValidData()
    {
        return $this->validData;
    }

    /**
     * @param mixed $validData
     */
    public function setValidData($validData)
    {
        $this->validData = $validData;
        $this->stopPropagation();
    }
}
