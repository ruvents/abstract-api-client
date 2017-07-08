<?php

namespace Ruvents\AbstractApiClient\Event;

use Ruvents\AbstractApiClient\Common\ContextRequestTrait;
use Ruvents\AbstractApiClient\Common\ContextResponseTrait;
use Ruvents\AbstractApiClient\Common\ContextTrait;
use Symfony\Component\EventDispatcher\Event;

class PostDecodeEvent extends Event
{
    use ContextTrait;
    use ContextRequestTrait;
    use ContextResponseTrait;

    /**
     * @var mixed
     */
    private $data;

    public function __construct(array $context, $data)
    {
        $this->context = $context;
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
