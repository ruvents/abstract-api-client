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

        $optionsResolver = new OptionsResolver();
        $this->service->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);

        $this->contextResolver = new OptionsResolver();
        $this->service->configureRequestContext($this->contextResolver);
        $this->contextResolver
            ->setDefined($optionsResolver->getDefinedOptions())
            ->setDefined(['api_client', 'data', 'request', 'response']);

        $this->eventDispatcher = new EventDispatcher();

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
            $context = $this->applyImmutableContextValues($context);
            $context = $this->contextResolver->resolve($context);
            $context = $this->applyImmutableContextValues($context);
            $context['request'] = $this->service->createRequest($context);

            // dispatch PRE_SEND event
            $preSendEvent = new PreSendEvent($context);
            $this->eventDispatcher->dispatch(Events::PRE_SEND, $preSendEvent);
            $context = $preSendEvent->getContext();

            // terminate if data was set
            if (null !== $context['data']) {
                return $context['data'];
            }

            // make http request
            $context['response'] = $this->service->sendRequest($context['request'], $context);

            // dispatch POST_SEND event
            $postSendEvent = new PostSendEvent($context);
            $this->eventDispatcher->dispatch(Events::POST_SEND, $postSendEvent);

            // validate response
            $this->service->validateResponse($context['response'], $context);

            // decode response
            $context['data'] = $this->service->decodeResponse($context['response'], $context);

            // validate data
            $this->service->validateData($context['data'], $context);

            // dispatch POST_DECODE event
            $postDecodeEvent = new PostDecodeEvent($context);
            $this->eventDispatcher->dispatch(Events::POST_DECODE, $postDecodeEvent);
            $context = $postDecodeEvent->getContext();

            return $context['data'];
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
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return ServiceInterface
     */
    protected function getService()
    {
        return $this->service;
    }

    /**
     * @param array $context
     *
     * @return array
     */
    private function applyImmutableContextValues(array $context)
    {
        return array_replace($context, $this->options, [
            'api_client' => $this,
        ]);
    }
}
