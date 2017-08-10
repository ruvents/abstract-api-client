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
     * @param ServiceInterface     $service
     * @param array                $defaultContext
     * @param ExtensionInterface[] $extensions
     */
    public function __construct(ServiceInterface $service, array $defaultContext = [], array $extensions = [])
    {
        $this->service = $service;
        $this->defaultContext = $defaultContext;
        $this->eventDispatcher = new EventDispatcher();
        $this->contextResolver = new OptionsResolver();

        $this->service->configureContext($this->contextResolver);
        $this->contextResolver->setDefined(['api_client', 'request', 'response', 'data']);

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
    public function request(array $context = [])
    {
        try {
            $context = $this->resolveContext($context);
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
     * @param array $context
     *
     * @return array
     */
    final protected function resolveContext(array $context)
    {
        $context = array_replace($this->defaultContext, $context, [
            'api_client' => $this,
            'request' => null,
            'response' => null,
            'data' => null,
        ]);

        return $this->contextResolver->resolve($context);
    }

    /**
     * @return ServiceInterface
     */
    final protected function getService()
    {
        return $this->service;
    }
}
