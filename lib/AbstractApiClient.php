<?php

namespace Ruvents\AbstractApiClient;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\RequestFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ruvents\AbstractApiClient\Event\ApiClientEvents;
use Ruvents\AbstractApiClient\Event\PostDecodeEvent;
use Ruvents\AbstractApiClient\Event\PostSendEvent;
use Ruvents\AbstractApiClient\Event\PreSendEvent;
use Ruvents\AbstractApiClient\Exception\DecodeException;
use Ruvents\AbstractApiClient\Extension\ApiClientExtensionInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractApiClient
{
    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var array
     */
    protected $facades;

    /**
     * @var array
     */
    private $options;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var OptionsResolver
     */
    private $contextResolver;

    /**
     * @param array                         $options
     * @param ApiClientExtensionInterface[] $extensions
     */
    public function __construct(array $options, array $extensions = [])
    {
        $this->configureDependencies($resolver = new OptionsResolver());
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);

        $this->httpClient = $this->options['http_client'];
        $this->requestFactory = $this->options['request_factory'];

        $this->contextResolver = new OptionsResolver();
        $this->configureContext($this->contextResolver);

        $eventDispatcher = $this->options['event_dispatcher'];

        foreach ($extensions as $extension) {
            $extension->configureContext($this->contextResolver);
            $eventDispatcher->addSubscriber($extension);
        }

        $this->eventDispatcher = new ImmutableEventDispatcher($eventDispatcher);
    }

    /**
     * @param RequestInterface $request
     * @param array            $context
     *
     * @return mixed
     */
    final public function request(RequestInterface $request, array $context = [])
    {
        $context = $this->contextResolver->resolve($context);

        $this->modifyRequest($request, $context);

        $preSendEvent = new PreSendEvent($context, $request);
        $this->eventDispatcher->dispatch(ApiClientEvents::PRE_SEND, $preSendEvent);

        $request = $preSendEvent->getRequest();
        $response = $preSendEvent->getResponse() ?: $this->httpClient->sendRequest($request);

        $this->validateResponse($response, $context);

        $postSendEvent = new PostSendEvent($context, $request, $response);
        $this->eventDispatcher->dispatch(ApiClientEvents::POST_SEND, $postSendEvent);

        $data = $this->decodeResponse($response, $context);
        $this->validateData($data, $context);

        $postDecodeEvent = new PostDecodeEvent($context, $request, $response, $data);
        $this->eventDispatcher->dispatch(ApiClientEvents::POST_DECODE, $postDecodeEvent);

        return $postDecodeEvent->getData();
    }

    abstract protected function configureOptions(OptionsResolver $resolver);

    abstract protected function configureContext(OptionsResolver $resolver);

    abstract protected function modifyRequest(RequestInterface &$request, array $context);

    abstract protected function validateResponse(ResponseInterface $response, array $context);

    /**
     * @param ResponseInterface $response
     * @param array             $context
     *
     * @throws DecodeException
     *
     * @return mixed
     */
    abstract protected function decodeResponse(ResponseInterface $response, array $context);

    abstract protected function validateData($data, array $context);

    /**
     * @param string $class
     *
     * @return mixed
     */
    protected function getFacade($class)
    {
        if (!isset($this->facades[$class])) {
            $this->facades[$class] = $this->createFacade($class);
        }

        return $this->facades[$class];
    }

    /**
     * @param string $class
     *
     * @return mixed
     */
    protected function createFacade($class)
    {
        return new $class($this, $this->requestFactory);
    }

    /**
     * @return array
     */
    final protected function getOptions()
    {
        return $this->options;
    }

    private function configureDependencies(OptionsResolver $resolver)
    {
        /** @noinspection PhpUnusedParameterInspection */
        $resolver
            ->setDefaults([
                'http_client' => function (Options $options) {
                    return HttpClientDiscovery::find();
                },
                'request_factory' => function (Options $options) {
                    return MessageFactoryDiscovery::find();
                },
                'event_dispatcher' => function (Options $options) {
                    return new EventDispatcher();
                },
            ])
            ->setAllowedTypes('http_client', 'Http\Client\HttpClient')
            ->setAllowedTypes('request_factory', 'Http\Message\MessageFactory')
            ->setAllowedTypes('event_dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }
}
