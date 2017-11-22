<?php

namespace Ruvents\AbstractApiClient\ContextBuilder;

use Ruvents\AbstractApiClient\ApiClientInterface;

abstract class AbstractContextBuilder
{
    public $context = [];

    protected $client;

    public function __construct(ApiClientInterface $client)
    {
        $this->client = $client;
    }

    public function __call($name, array $args)
    {
        if ('set' !== substr($name, 0, 3)) {
            throw new \BadMethodCallException(sprintf('Only setter methods are supported, "%s" called.', $name));
        }

        if (count($args) < 1) {
            throw new \BadMethodCallException('This method requires one argument.');
        }

        return $this->applySetter(substr($name, 3), $args[0]);
    }

    public function getResult()
    {
        return $this->client->request($this->context);
    }

    protected abstract function applySetter($param, $value);
}
