<?php

namespace Ruvents\AbstractApiClient\Service;

abstract class AbstractApiService implements ApiServiceInterface
{
    use HttpClientDiscoveryTrait;
    use Response200Trait;
    use JsonDecodeTrait;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [];
    }
}
