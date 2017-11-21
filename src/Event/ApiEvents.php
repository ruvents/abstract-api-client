<?php

namespace Ruvents\AbstractApiClient\Event;

final class ApiEvents
{
    /**
     * @Event("Ruvents\AbstractApiClient\Event\PreSendEvent")
     */
    const PRE_SEND = 'ruvents.abstract_api_client.pre_send';

    /**
     * @Event("Ruvents\AbstractApiClient\Event\PostSendEvent")
     */
    const POST_SEND = 'ruvents.abstract_api_client.post_send';

    /**
     * @Event("Ruvents\AbstractApiClient\Event\PostDecodeEvent")
     */
    const POST_DECODE = 'ruvents.abstract_api_client.post_decode';

    /**
     * @Event("Ruvents\AbstractApiClient\Event\ErrorEvent")
     */
    const ERROR = 'ruvents.abstract_api_client.error';
}
