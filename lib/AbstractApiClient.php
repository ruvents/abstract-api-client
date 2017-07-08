<?php

namespace Ruvents\AbstractApiClient;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
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
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractApiClient
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

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
     * @param HttpClient|null               $httpClient
     * @param EventDispatcherInterface|null $dispatcher
     */
    public function __construct(
        array $options,
        array $extensions = [],
        HttpClient $httpClient = null,
        EventDispatcherInterface $dispatcher = null
    ) {
        $this->configureOptions($resolver = new OptionsResolver());
        $this->options = $resolver->resolve($options);

        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();

        $this->contextResolver = new OptionsResolver();
        $this->configureContext($this->contextResolver);

        $dispatcher = $dispatcher ?: new EventDispatcher();

        foreach ($extensions as $extension) {
            $extension->configureContext($this->contextResolver, $this->options);
            $dispatcher->addSubscriber($extension);
        }

        $this->dispatcher = new ImmutableEventDispatcher($dispatcher);
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
        $this->dispatcher->dispatch(ApiClientEvents::PRE_SEND, $preSendEvent);

        $request = $preSendEvent->getRequest();
        $response = $preSendEvent->getResponse() ?: $this->httpClient->sendRequest($request);

        $this->validateResponse($response, $context);

        $postSendEvent = new PostSendEvent($context, $request, $response);
        $this->dispatcher->dispatch(ApiClientEvents::POST_SEND, $postSendEvent);

        $data = $this->decodeResponse($response, $context);
        $this->validateData($data, $context);

        $postDecodeEvent = new PostDecodeEvent($context, $request, $response, $data);
        $this->dispatcher->dispatch(ApiClientEvents::POST_DECODE, $postDecodeEvent);

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
     * @return array
     */
    final protected function getOptions()
    {
        return $this->options;
    }
}
