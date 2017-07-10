<?php

namespace Ruvents\AbstractApiClient\Event;

use Ruvents\AbstractApiClient\Common;
use Ruvents\AbstractApiClient\Exception\ErrorEventException;
use Symfony\Component\EventDispatcher\Event;

class ErrorEvent extends Event
{
    use Common\ContextTrait;
    use Common\ContextRequestTrait;
    use Common\ContextResponseTrait;
    use Common\ContextDataTrait;

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @var mixed
     */
    private $validData;

    public function __construct(ErrorEventException $exception)
    {
        $this->context = $exception->getContext();
        $this->exception = $exception;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param \Exception $exception
     */
    public function setException(\Exception $exception)
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
