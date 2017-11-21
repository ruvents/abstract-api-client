<?php

namespace Ruvents\AbstractApiClient\Event;

use Ruvents\AbstractApiClient\ApiClientInterface;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractEvent extends Event
{
    /**
     * @var ApiClientInterface
     */
    private $client;

    /**
     * @var array
     */
    protected $context;

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

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }
}
