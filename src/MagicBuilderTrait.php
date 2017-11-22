<?php

namespace Ruvents\AbstractApiClient;

trait MagicBuilderTrait
{
    private $builderNamespace;

    public function __call($name, array $arguments)
    {
        if (!preg_match('/^([0-9a-z]+)([A-Z].*)?$/', $name, $matches)) {
            throw new \BadMethodCallException(sprintf('Method "%s" does not respect the naming convention.', $name));
        }

        if (null === $this->builderNamespace) {
            $clientClass = get_class($this);
            $this->builderNamespace = substr($clientClass, 0, strrpos($clientClass, '\\')).'\\Builder\\';
        }

        $class = $this->builderNamespace.ucfirst($matches[1]).(isset($matches[2]) ? '\\'.$matches[2] : '').'Builder';

        return new $class($this);
    }
}
