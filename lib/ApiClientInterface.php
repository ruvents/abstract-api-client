<?php

namespace Ruvents\AbstractApiClient;

interface ApiClientInterface
{
    /**
     * @param array $context
     *
     * @return mixed
     */
    public function request(array $context = []);
}
