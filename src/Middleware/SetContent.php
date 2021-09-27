<?php

namespace Loouss\ObsClient\Middleware;

use Psr\Http\Message\RequestInterface;

class SetContent
{
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $request = $request->withHeader(
                'Content-MD5',
                base64_encode(md5($request->getBody()->getContents(), true))
            )->withHeader('Date', gmdate('D, d M Y H:i:s \G\M\T'));

            return $handler($request, $options);
        };
    }
}
