<?php

namespace Ruvents\AbstractApiClient\Event;

use Ruvents\AbstractApiClient\ApiClientInterface;
use Ruvents\AbstractApiClient\Common\ContextTrait;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractEvent extends Event
{
    use ContextTrait;

    /**
     * @var ApiClientInterface
     */
    private $client;

    /**
     * @param ApiClientInterface $client
     * @param array              $context
     */
    public function __construct(ApiClientInterface $client, array $context)
    {
        $this->client = $client;
        $this->context = $context;
    }

    /**
     * @return ApiClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }
}
