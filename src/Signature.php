<?php

namespace Loouss\ObsClient;

use Psr\Http\Message\RequestInterface;

class Signature
{
    public string $accessKey;
    public string $secretKey;
    public ?string $bucket;

    public function __construct(string $accessKey, string $secretKey, ?string $bucket = null)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->bucket = $bucket;
    }

    public function createAuthorizationHeader(RequestInterface $request, ?string $expires = null): string
    {
        $buffer[] = $request->getMethod();
        $buffer[] = "\n";
        $buffer[] = $request->getHeaderLine('Content-MD5');
        $buffer[] = "\n";
        $buffer[] = $request->getHeaderLine('Content-Type');
        $buffer[] = "\n";
        $buffer[] = $request->getHeaderLine('Date');
        $buffer[] = "\n";

        if ($request->getHeaderLine('x-obs-storage-class')) {
            $buffer[] = $request->getHeaderLine('Date');
            $buffer[] = "\n";
        }

        $buffer[] = '/' . $this->bucket . $request->getUri()->getPath();
        $stringToSign = implode('', $buffer);
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $this->secretKey, true));

        return 'OBS' . ' ' . $this->accessKey . ':' . $signature;
    }

}
