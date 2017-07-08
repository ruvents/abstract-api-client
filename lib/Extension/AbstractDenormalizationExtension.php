<?php

namespace Ruvents\AbstractApiClient\Extension;

use Psr\Http\Message\RequestInterface;
use Ruvents\AbstractApiClient\Event\ApiClientEvents;
use Ruvents\AbstractApiClient\Event\PostDecodeEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

abstract class AbstractDenormalizationExtension implements ApiClientExtensionInterface
{
    /**
     * @var DenormalizerInterface
     */
    protected $denormalizer;

    public function __construct(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function configureContext(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'denormalize' => true,
                'class' => null,
            ])
            ->setAllowedTypes('denormalize', 'bool')
            ->setAllowedTypes('class', ['null', 'string']);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ApiClientEvents::POST_DECODE => 'denormalize',
        ];
    }

    public function denormalize(PostDecodeEvent $event)
    {
        $context = $event->getContext();
        $data = $event->getData();

        /** @var RequestInterface $request */
        $request = $context['_request'];

        if (!$context['denormalize']) {
            return;
        }

        $class = $context['class'] ?: $this->getClass($request);

        $event->setData($this->denormalizer->denormalize($data, $class));
    }

    /**
     * @return string|null
     */
    abstract protected function getClass(RequestInterface $request);
}
