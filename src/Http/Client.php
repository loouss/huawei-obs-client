<?php

namespace Loouss\ObsClient\Http;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use Loouss\ObsClient\Middleware\CreateRequestSignature;
use Loouss\ObsClient\Middleware\SetContent;

class Client
{
    protected string $ak;
    protected string $sk;
    protected string $endpoint;
    protected string $bucket;
    protected ?HandlerStack $handlerStack = null;
    protected array $middlewares = [];

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
            array_push($this->middlewares, $middleware);
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
        //$arguments[1]['debug'] = true;
        try {
            return $this->getHttpClient()->$method($arguments[0], $arguments[1]);
        } catch (ClientException | ServerException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

    public function getHttpClient(): \GuzzleHttp\Client
    {
        $options['base_uri'] = \sprintf('https://%s.'.$this->endpoint.'/', $this->bucket);
        return $this->client ?? $this->client = $this->createHttpClient($options);
    }

    public function createHttpClient(array $options = []): \GuzzleHttp\Client
    {
        return new \GuzzleHttp\Client(array_merge([
            'handler' => $this->getHandlerStack(),
        ], $options));
    }

    public function getHandlerStack(): HandlerStack
    {
        if ($this->handlerStack) {
            return $this->handlerStack;
        }

        $this->handlerStack = HandlerStack::create();

        foreach ($this->middlewares as $name => $middleware) {
            $this->handlerStack->unshift($middleware, $name);
        }

        return $this->handlerStack;
    }
}