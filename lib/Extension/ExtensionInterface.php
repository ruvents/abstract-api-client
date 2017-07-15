<?php

namespace Ruvents\AbstractApiClient\Extension;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ExtensionInterface extends EventSubscriberInterface
{
    public function configureContext(OptionsResolver $resolver);
}
