<?php

namespace Loouss\ObsClient\Middleware;

use Loouss\ObsClient\Signature;
use Psr\Http\Message\RequestInterface;

class CreateRequestSignature
{
    protected string $secretId;

    protected string $secretKey;

    protected ?string $signatureExpires;

    protected ?string $bucket;


    public function __construct(string $secretId, string $secretKey, ?string $bucket, ?string $signatureExpires = null)
    {
        $this->secretId = $secretId;
        $this->secretKey = $secretKey;
        $this->signatureExpires = $signatureExpires;
        $this->bucket = $bucket;
    }

    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $request = $request->withHeader(
                'Authorization',
                (new Signature($this->secretId, $this->secretKey, $this->bucket))
                    ->createAuthorizationHeader($request, $this->signatureExpires)
            );

            return $handler($request, $options);
        };
    }
}
