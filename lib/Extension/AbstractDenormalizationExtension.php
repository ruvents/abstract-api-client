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
    public function configureContext(OptionsResolver $resolver, array $options)
    {
        $resolver
            ->setDefaults([
                'denormalize' => true,
            ])
            ->setAllowedTypes('denormalize', 'bool');
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

        if (!$context['denormalize'] || null === $class = $this->getClass($event->getRequest())) {
            return;
        }

        $data = $event->getData();

        if ($this->denormalizer->supportsDenormalization($data, $class)) {
            $event->setData($this->denormalizer->denormalize($data, $class));
        }
    }

    /**
     * @return string|null
     */
    abstract protected function getClass(RequestInterface $request);
}
