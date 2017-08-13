<?php

namespace Ruvents\AbstractApiClient\Extension;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ExtensionInterface extends EventSubscriberInterface
{
    /**
     * @param OptionsResolver $resolver
     * @param array           $options
     *
     * @return void
     */
    public function configureRequestContext(OptionsResolver $resolver, array $options);
}
