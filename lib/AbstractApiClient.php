<?php

namespace Ruvents\AbstractApiClient;

use Ruvents\AbstractApiClient\Event\ErrorEvent;
use Ruvents\AbstractApiClient\Event\Events;
use Ruvents\AbstractApiClient\Event\PostDecodeEvent;
use Ruvents\AbstractApiClient\Event\PostSendEvent;
use Ruvents\AbstractApiClient\Event\PreSendEvent;
use Ruvents\AbstractApiClient\Exception\ErrorEventException;
use Ruvents\AbstractApiClient\Extension\ExtensionInterface;
use Ruvents\AbstractApiClient\Service\ServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractApiClient implements ApiClientInterface
{
    const CONTEXT_API_CLIENT = 'api_client';
    const CONTEXT_DATA = 'data';
    const CONTEXT_OPTIONS = 'options';
    const CONTEXT_REQUEST = 'request';
    const CONTEXT_RESPONSE = 'response';

    /**
     * @var ServiceInterface
     */
    private $service;

    /**
     * @var array
     */
    private $options;

    /**
     * @var OptionsResolver
     */
    private $contextResolver;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param ServiceInterface     $service
     * @param array                $options
     * @param ExtensionInterface[] $extensions
     */
    public function __construct(ServiceInterface $service, array $options = [], array $extensions = [])
    {
        $this->service = $service;

        // configure options
        $optionsResolver = new OptionsResolver();
        $this->service->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);

        // configure context
        $this->contextResolver = new OptionsResolver();
        $this->service->configureRequestContext($this->contextResolver, $this->options);
        $this->contextResolver->setDefined([
            self::CONTEXT_API_CLIENT,
            self::CONTEXT_DATA,
            self::CONTEXT_OPTIONS,
            self::CONTEXT_REQUEST,
            self::CONTEXT_RESPONSE,
        ]);

        $this->eventDispatcher = new EventDispatcher();

        // register extensions
        foreach ($extensions as $extension) {
            $extension->configureRequestContext($this->contextResolver, $this->options);
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
            $context = $this->setImmutableContextValues($context);
            $context = $this->contextResolver->resolve($context);
            $context = $this->setImmutableContextValues($context);
            $context[self::CONTEXT_REQUEST] = $this->service->createRequest($context, $this);

            // dispatch PRE_SEND event
            $preSendEvent = new PreSendEvent($context);
            $this->eventDispatcher->dispatch(Events::PRE_SEND, $preSendEvent);
            $context = $preSendEvent->getContext();

            // terminate if data was set
            if (null !== $context[self::CONTEXT_DATA]) {
                return $context[self::CONTEXT_DATA];
            }

            // make http request
            $context[self::CONTEXT_RESPONSE] = $this->service
                ->sendRequest($context[self::CONTEXT_REQUEST], $context);

            // dispatch POST_SEND event
            $postSendEvent = new PostSendEvent($context);
            $this->eventDispatcher->dispatch(Events::POST_SEND, $postSendEvent);

            // validate response
            $this->service->validateResponse($context[self::CONTEXT_RESPONSE], $context);

            // decode response
            $context[self::CONTEXT_DATA] = $this->service
                ->decodeResponse($context[self::CONTEXT_RESPONSE], $context);

            // validate data
            $this->service->validateData($context[self::CONTEXT_DATA], $context);

            // dispatch POST_DECODE event
            $postDecodeEvent = new PostDecodeEvent($context);
            $this->eventDispatcher->dispatch(Events::POST_DECODE, $postDecodeEvent);
            $context = $postDecodeEvent->getContext();

            return $context[self::CONTEXT_DATA];
        } catch (ErrorEventException $exception) {
            // dispatch ERROR event
            $errorEvent = new ErrorEvent($exception);
            $this->eventDispatcher->dispatch(Events::ERROR, $errorEvent);

            // return valid data if it was provided
            if (null !== $data = $errorEvent->getValidData()) {
                return $data;
            }

            throw $errorEvent->getException();
        }
    }

    /**
     * @return ServiceInterface
     */
    protected function getService()
    {
        return $this->service;
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $context
     *
     * @return array
     */
    private function setImmutableContextValues(array $context)
    {
        return array_replace($context, [
            self::CONTEXT_API_CLIENT => $this,
            self::CONTEXT_OPTIONS => $this->options,
        ]);
    }
}
