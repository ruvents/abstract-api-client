<?php

namespace Ruvents\AbstractApiClient;

use Http\Client\Exception as HttpClientException;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\MessageFactory;
use Http\Message\UriFactory;
use Ruvents\AbstractApiClient\Definition\ApiDefinitionInterface;
use Ruvents\AbstractApiClient\Definition\ApiExtensionInterface;
use Ruvents\AbstractApiClient\Event\ApiEvents;
use Ruvents\AbstractApiClient\Event\ErrorEvent;
use Ruvents\AbstractApiClient\Event\PostDecodeEvent;
use Ruvents\AbstractApiClient\Event\PostSendEvent;
use Ruvents\AbstractApiClient\Event\PreSendEvent;
use Ruvents\AbstractApiClient\Exception\ApiExceptionInterface;
use Ruvents\AbstractApiClient\Exception\RequestException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractApiClient implements ApiClientInterface
{
    const CONTEXT_REQUEST = '_request';
    const CONTEXT_RESPONSE = '_response';
    const CONTEXT_RESPONSE_DATA = '_response_data';

    /**
     * @var UriFactory
     */
    protected $uriFactory;

    /**
     * @var MessageFactory
     */
    protected $requestFactory;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var ApiDefinitionInterface
     */
    private $definition;

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
     * @param ApiDefinitionInterface  $definition
     * @param array                   $defaultContext
     * @param ApiExtensionInterface[] $extensions
     */
    public function __construct(ApiDefinitionInterface $definition, array $defaultContext = [], array $extensions = [])
    {
        $this->definition = $definition;
        $this->eventDispatcher = new EventDispatcher();
        $this->eventDispatcher->addSubscriber($this->definition);

        // configure default context
        $this->contextResolver = new OptionsResolver();
        $this->definition->configureDefaultContext($this->contextResolver);
        foreach ($extensions as $extension) {
            $extension->configureDefaultContext($this->contextResolver);
        }
        $this->defaultContext = $this->contextResolver->resolve($defaultContext);

        // configure request context
        $this->contextResolver
            ->setDefined([self::CONTEXT_REQUEST, self::CONTEXT_RESPONSE, self::CONTEXT_RESPONSE_DATA])
            ->setDefaults($this->defaultContext);
        $this->definition->configureRequestContext($this->contextResolver);

        // register extensions
        foreach ($extensions as $extension) {
            $extension->configureRequestContext($this->contextResolver);
            $this->eventDispatcher->addSubscriber($extension);
        }

        $this->uriFactory = UriFactoryDiscovery::find();
        $this->requestFactory = MessageFactoryDiscovery::find();
        $this->httpClient = HttpClientDiscovery::find();
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
                self::CONTEXT_REQUEST => null,
                self::CONTEXT_RESPONSE => null,
                self::CONTEXT_RESPONSE_DATA => null,
            ]);

            // create request
            $context[self::CONTEXT_REQUEST] = $this->definition
                ->createRequest($this->uriFactory, $this->requestFactory, $this, $context);

            // dispatch PRE_SEND event
            $preSendEvent = new PreSendEvent($this, $context);
            $this->eventDispatcher->dispatch(ApiEvents::PRE_SEND, $preSendEvent);
            $context = $preSendEvent->getContext();

            // terminate if data was set
            if (null !== $context[self::CONTEXT_RESPONSE_DATA]) {
                return $context[self::CONTEXT_RESPONSE_DATA];
            }

            // send request
            try {
                $context[self::CONTEXT_RESPONSE] = $this->httpClient
                    ->sendRequest($context[self::CONTEXT_REQUEST]);
            } catch (HttpClientException $exception) {
                throw new RequestException($context[self::CONTEXT_REQUEST], 'Failed to process request.', 0, $exception);
            }

            // dispatch POST_SEND event
            $postSendEvent = new PostSendEvent($this, $context);
            $this->eventDispatcher->dispatch(ApiEvents::POST_SEND, $postSendEvent);

            // validate response
            $this->definition->validateResponse($context[self::CONTEXT_RESPONSE], $context);

            // decode response
            $context[self::CONTEXT_RESPONSE_DATA] = $this->definition
                ->decodeResponse($context[self::CONTEXT_RESPONSE], $context);

            // validate data
            $this->definition->validateData($context[self::CONTEXT_RESPONSE_DATA], $context);

            // dispatch POST_DECODE event
            $postDecodeEvent = new PostDecodeEvent($this, $context);
            $this->eventDispatcher->dispatch(ApiEvents::POST_DECODE, $postDecodeEvent);
            $context = $postDecodeEvent->getContext();

            return $context[self::CONTEXT_RESPONSE_DATA];
        } catch (ApiExceptionInterface $exception) {
            // dispatch ERROR event
            $errorEvent = new ErrorEvent($exception, $this, $context);
            $this->eventDispatcher->dispatch(ApiEvents::ERROR, $errorEvent);

            // return valid data if it was provided
            if (null !== $data = $errorEvent->getValidData()) {
                return $data;
            }

            throw $errorEvent->getException();
        }
    }

    /**
     * @return ApiDefinitionInterface
     */
    protected function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @return array
     */
    protected function getDefaultContext()
    {
        return $this->defaultContext;
    }
}
