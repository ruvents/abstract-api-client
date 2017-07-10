<?php

namespace Ruvents\AbstractApiClient;

use Ruvents\AbstractApiClient\Event\ApiClientEvents;
use Ruvents\AbstractApiClient\Event\ErrorEvent;
use Ruvents\AbstractApiClient\Event\PostDecodeEvent;
use Ruvents\AbstractApiClient\Event\PostSendEvent;
use Ruvents\AbstractApiClient\Event\PreSendEvent;
use Ruvents\AbstractApiClient\Exception\ErrorEventException;
use Ruvents\AbstractApiClient\Extension\ApiExtensionInterface;
use Ruvents\AbstractApiClient\Service\ApiServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractApiClient
{
    /**
     * @var OptionsResolver
     */
    private $contextResolver;

    /**
     * @var array
     */
    private $defaultContext;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param array                         $defaultContext
     * @param ApiExtensionInterface[]       $extensions
     * @param null|EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        array $defaultContext = [],
        array $extensions = [],
        EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->defaultContext = $defaultContext;
        $this->eventDispatcher = $eventDispatcher ?: new EventDispatcher();
        $this->contextResolver = new OptionsResolver();

        $this->getService()->configureContext($this->contextResolver);

        foreach ($extensions as $extension) {
            $this->eventDispatcher->addSubscriber($extension);
            $extension->configureContext($this->contextResolver);
        }
    }

    /**
     * @param array $context
     *
     * @return mixed
     * @throws \Exception
     */
    final public function request(array $context = [])
    {
        try {
            // resolve context
            $context = array_replace($this->defaultContext, $context);
            $context = $this->contextResolver->resolve($context);

            // define array offsets
            $context['request'] = $this->getService()->createRequest($context);
            $context['response'] = null;
            $context['data'] = null;

            // dispatch PRE_SEND event
            $preSendEvent = new PreSendEvent($context);
            $this->eventDispatcher->dispatch(ApiClientEvents::PRE_SEND, $preSendEvent);
            $context = $preSendEvent->getContext();

            // terminate if data was set
            if (null !== $context['data']) {
                return $context['data'];
            }

            // make http request
            $context['response'] = $this->getService()->sendRequest($context['request'], $context);

            // dispatch POST_SEND event
            $postSendEvent = new PostSendEvent($context);
            $this->eventDispatcher->dispatch(ApiClientEvents::POST_SEND, $postSendEvent);

            // validate response
            $this->getService()->validateResponse($context['response'], $context);

            // decode response
            $context['data'] = $this->getService()->decodeResponse($context['response'], $context);

            // validate data
            $this->getService()->validateData($context['data'], $context);

            // dispatch POST_DECODE event
            $postDecodeEvent = new PostDecodeEvent($context);
            $this->eventDispatcher->dispatch(ApiClientEvents::POST_DECODE, $postDecodeEvent);
            $context = $postDecodeEvent->getContext();

            return $context['data'];
        } catch (ErrorEventException $exception) {
            // dispatch ERROR event
            $errorEvent = new ErrorEvent($exception);
            $this->eventDispatcher->dispatch(ApiClientEvents::ERROR, $errorEvent);

            // return valid data if it was provided
            if (null !== $data = $errorEvent->getValidData()) {
                return $data;
            }

            throw $errorEvent->getException();
        }
    }

    /**
     * @return ApiServiceInterface
     */
    abstract protected function getService();
}
