<?php

namespace Ruvents\AbstractApiClient\Extension;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ApiExtensionInterface extends EventSubscriberInterface
{
    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureDefaultContext(OptionsResolver $resolver);

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureRequestContext(OptionsResolver $resolver);
}
