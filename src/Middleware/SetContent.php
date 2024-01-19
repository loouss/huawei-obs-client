<?php

declare(strict_types=1);

namespace Loouss\ObsClient\Middleware;

use GuzzleHttp\Psr7\MimeType;
use Psr\Http\Message\RequestInterface;

class SetContent
{
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $request = $request->withHeader('Content-MD5', base64_encode(md5($request->getBody()->getContents(), true)))
                ->withHeader('Date', gmdate('D, d M Y H:i:s \G\M\T'))
                ->withHeader('Content-Type', MimeType::fromFilename($request->getUri()->getPath()));
            return $handler($request, $options);
        };
    }
}
