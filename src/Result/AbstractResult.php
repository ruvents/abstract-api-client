<?php

namespace Ruvents\AbstractApiClient\Result;

abstract class AbstractResult
{
    /**
     * @var array
     */
    private $result;

    /**
     * @var string[]
     */
    private $unprocessedMap;

    /**
     * @param array $result
     */
    public function __construct(array $result)
    {
        $this->result = $result;
        $this->unprocessedMap = $this->getMap();
    }

    /**
     * @param string $offset
     *
     * @return bool
     */
    final public function __isset($offset)
    {
        return isset($this->result[$offset]);
    }

    /**
     * @param string $offset
     *
     * @return mixed
     */
    final public function __get($offset)
    {
        if (!isset($this->result[$offset])) {
            return null;
        }

        if (isset($this->unprocessedMap[$offset])) {
            $this->result[$offset] = ResultDenormalizer::denormalize($this->result[$offset], $this->unprocessedMap[$offset]);

            unset($this->unprocessedMap[$offset]);
        }

        return $this->result[$offset];
    }

    /**
     * @param string $offset
     * @param mixed  $value
     *
     * @throws \LogicException
     */
    final public function __set($offset, $value)
    {
        throw new \LogicException('This object is immutable.');
    }

    /**
     * @return string[]
     */
    protected function getMap()
    {
        return [];
    }
}
