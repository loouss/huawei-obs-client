<?php

declare(strict_types=1);

namespace Loouss\ObsClient\Http;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use Hyperf\Guzzle\CoroutineHandler;
use Loouss\ObsClient\Middleware\CreateRequestSignature;
use Loouss\ObsClient\Middleware\SetContent;

class Client
{
    protected string $ak;
    protected string $sk;
    protected string $endpoint;
    protected string $bucket;
    protected array $middlewares = [];

    protected ?HandlerStack $handlerStack = null;
    protected \GuzzleHttp\Client $client;

    public function __construct(string $ak, string $sk, string $endpoint, string $bucket, array $config = [])
    {
        $this->ak = $ak;
        $this->sk = $sk;
        $this->endpoint = $endpoint;
        $this->bucket = $bucket;
        $this->pushMiddleware(
            new CreateRequestSignature($this->ak, $this->sk, $this->bucket)
        );
        $this->pushMiddleware(new SetContent());
    }

    public function pushMiddleware(callable $middleware, string $name = null): Client
    {
        if (!is_null($name)) {
            $this->middlewares[$name] = $middleware;
        } else {
            $this->middlewares[] = $middleware;
        }

        return $this;
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $arguments)
    {
        try {
            return $this->getHttpClient()->$method($arguments[0], $arguments[1]);
        } catch (ClientException|ServerException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

    public function getHttpClient(): \GuzzleHttp\Client
    {
        $options['base_uri'] = \sprintf('https://%s.' . $this->endpoint . '/', $this->bucket);
        return $this->client ?? $this->client = $this->createHttpClient($options);
    }

    /**
     * @param array $options
     * @return \GuzzleHttp\Client
     */
    public function createHttpClient(array $options = []): \GuzzleHttp\Client
    {
        return new \GuzzleHttp\Client(array_merge([
            'handler' => $this->getHandlerStack(),
        ], $options));
    }

    /**
     * @return HandlerStack
     */
    public function getHandlerStack(): HandlerStack
    {
        if ($this->handlerStack) {
            return $this->handlerStack;
        }

        if (class_exists(CoroutineHandler::class)) {
            $this->handlerStack = HandlerStack::create(new CoroutineHandler());
        } else {
            $this->handlerStack = HandlerStack::create();
        }

        foreach ($this->middlewares as $name => $middleware) {
            $this->handlerStack->unshift($middleware, (string)$name);
        }

        return $this->handlerStack;
    }
}