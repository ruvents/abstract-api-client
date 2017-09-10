<?php

namespace Ruvents\AbstractApiClient;

use Ruvents\AbstractApiClient\Event\ApiEvents;
use Ruvents\AbstractApiClient\Event\ErrorEvent;
use Ruvents\AbstractApiClient\Event\PostDecodeEvent;
use Ruvents\AbstractApiClient\Event\PostSendEvent;
use Ruvents\AbstractApiClient\Event\PreSendEvent;
use Ruvents\AbstractApiClient\Exception\ApiExceptionInterface;
use Ruvents\AbstractApiClient\Extension\ApiExtensionInterface;
use Ruvents\AbstractApiClient\Service\ApiServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractApiClient implements ApiClientInterface
{
    const CONTEXT_REQUEST = '_request';
    const CONTEXT_RESPONSE = '_response';
    const CONTEXT_RESPONSE_DATA = '_response_data';

    /**
     * @var ApiServiceInterface
     */
    private $service;

    /**
     * @var array
     */
    private $defaultContext;

    /**
     * @var OptionsResolver
     */
    private $contextResolver;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param ApiServiceInterface     $service
     * @param array                   $defaultContext
     * @param ApiExtensionInterface[] $extensions
     */
    public function __construct(ApiServiceInterface $service, array $defaultContext = [], array $extensions = [])
    {
        $this->service = $service;
        $this->eventDispatcher = new EventDispatcher();
        $this->eventDispatcher->addSubscriber($this->service);

        // configure default context
        $this->contextResolver = new OptionsResolver();
        $this->service->configureDefaultContext($this->contextResolver);
        foreach ($extensions as $extension) {
            $extension->configureDefaultContext($this->contextResolver);
        }
        $this->defaultContext = $this->contextResolver->resolve($defaultContext);

        // configure request context
        $this->contextResolver
            ->setDefined([self::CONTEXT_REQUEST, self::CONTEXT_RESPONSE, self::CONTEXT_RESPONSE_DATA])
            ->setDefaults($this->defaultContext);
        $this->service->configureRequestContext($this->contextResolver);

        // register extensions
        foreach ($extensions as $extension) {
            $extension->configureRequestContext($this->contextResolver);
            $this->eventDispatcher->addSubscriber($extension);
        }
    }

    /**
     * @param array $context
     *
     * @return mixed
     * @throws \Exception
     */
    public function request(array $context = [])
    {
        try {
            // resolve context
            $context = array_merge($this->contextResolver->resolve($context), [
                self::CONTEXT_REQUEST => $this->service->createRequest($context, $this),
                self::CONTEXT_RESPONSE => null,
                self::CONTEXT_RESPONSE_DATA => null,
            ]);

            // dispatch PRE_SEND event
            $preSendEvent = new PreSendEvent($this, $context);
            $this->eventDispatcher->dispatch(ApiEvents::PRE_SEND, $preSendEvent);
            $context = $preSendEvent->getContext();

            // terminate if data was set
            if (null !== $context[self::CONTEXT_RESPONSE_DATA]) {
                return $context[self::CONTEXT_RESPONSE_DATA];
            }

            // make http request
            $context[self::CONTEXT_RESPONSE] = $this->service
                ->sendRequest($context[self::CONTEXT_REQUEST], $context);

            // dispatch POST_SEND event
            $postSendEvent = new PostSendEvent($this, $context);
            $this->eventDispatcher->dispatch(ApiEvents::POST_SEND, $postSendEvent);

            // validate response
            $this->service->validateResponse($context[self::CONTEXT_RESPONSE], $context);

            // decode response
            $context[self::CONTEXT_RESPONSE_DATA] = $this->service
                ->decodeResponse($context[self::CONTEXT_RESPONSE], $context);

            // validate data
            $this->service->validateData($context[self::CONTEXT_RESPONSE_DATA], $context);

            // dispatch POST_DECODE event
            $postDecodeEvent = new PostDecodeEvent($this, $context);
            $this->eventDispatcher->dispatch(ApiEvents::POST_DECODE, $postDecodeEvent);
            $context = $postDecodeEvent->getContext();

            return $context[self::CONTEXT_RESPONSE_DATA];
        } catch (ApiExceptionInterface $exception) {
            // dispatch ERROR event
            $errorEvent = new ErrorEvent($this, $exception);
            $this->eventDispatcher->dispatch(ApiEvents::ERROR, $errorEvent);

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
    protected function getService()
    {
        return $this->service;
    }

    /**
     * @return array
     */
    protected function getDefaultContext()
    {
        return $this->defaultContext;
    }
}
