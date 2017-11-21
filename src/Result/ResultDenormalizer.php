<?php

namespace Ruvents\AbstractApiClient\Result;

class ResultDenormalizer
{
    /**
     * @param mixed  $data
     * @param string $class Namespace\Class or Namespace\Class[]
     *
     * @return null|array|object
     */
    public static function denormalize($data, $class)
    {
        if (null === $data) {
            return null;
        }

        if ('[]' === substr($class, -2)) {
            $class = substr($class, 0, -2);

            return array_map(function ($data) use ($class) {
                return self::denormalize($data, $class);
            }, $data);
        }

        return new $class($data);
    }
}
