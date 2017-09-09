<?php

namespace Ruvents\AbstractApiClient\Event;

use Ruvents\AbstractApiClient\Common;
use Ruvents\AbstractApiClient\Exception\ApiExceptionInterface;
use Symfony\Component\EventDispatcher\Event;

class ErrorEvent extends Event
{
    use Common\ContextRequestTrait;
    use Common\ContextResponseTrait;
    use Common\ContextResponseDataTrait;

    /**
     * @var ApiExceptionInterface
     */
    private $exception;

    /**
     * @var mixed
     */
    private $validData;

    public function __construct(ApiExceptionInterface $exception)
    {
        $this->setException($exception);
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->exception->getContext();
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
