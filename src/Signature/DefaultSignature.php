<?php

namespace Loouss\ObsClient\Signature;

use Loouss\ObsClient\Constant\ObsClientConst;
use Loouss\ObsClient\Http\Common\Model;

class DefaultSignature extends AbstractSignature
{
    const INTEREST_HEADER_KEY_LIST = array('content-type', 'content-md5', 'date');

    public function __construct(
        $ak,
        $sk,
        $pathStyle,
        $endpoint,
        $methodName,
        $signature,
        $securityToken = false,
        $isCname = false
    ) {
        parent::__construct($ak, $sk, $pathStyle, $endpoint, $methodName, $signature, $securityToken, $isCname);
    }

    public function doAuth(array &$requestConfig, array &$params, Model $model): array
    {
        $result = $this->prepareAuth($requestConfig, $params, $model);

        $result['headers']['Date'] = gmdate('D, d M Y H:i:s \G\M\T');
        $canonicalstring = $this->makeCanonicalstring($result['method'], $result['headers'], $result['pathArgs'],
            $result['dnsParam'], $result['uriParam']);

        $result['cannonicalRequest'] = $canonicalstring;

        $signature = base64_encode(hash_hmac('sha1', $canonicalstring, $this->sk, true));

        $constants = ObsClientConst::OBS_CONSTANT;
        $signatureFlag = $constants::FLAG;

        $authorization = $signatureFlag . ' ' . $this->ak . ':' . $signature;

        $result['headers']['Authorization'] = $authorization;

        return $result;
    }

    public function makeCanonicalstring($method, $headers, $pathArgs, $bucketName, $objectKey, $expires = null): string
    {
        $buffer = [];
        $buffer[] = $method;
        $buffer[] = "\n";
        $interestHeaders = [];
        $constants = ObsClientConst::OBS_CONSTANT;

        foreach ($headers as $key => $value) {
            $key = strtolower($key);
            if (in_array($key, self::INTEREST_HEADER_KEY_LIST) || strpos($key, $constants::HEADER_PREFIX) === 0) {
                $interestHeaders[$key] = $value;
            }
        }

        if (array_key_exists($constants::ALTERNATIVE_DATE_HEADER, $interestHeaders)) {
            $interestHeaders['date'] = '';
        }

        if ($expires !== null) {
            $interestHeaders['date'] = strval($expires);
        }

        if (!array_key_exists('content-type', $interestHeaders)) {
            $interestHeaders['content-type'] = '';
        }

        if (!array_key_exists('content-md5', $interestHeaders)) {
            $interestHeaders['content-md5'] = '';
        }

        ksort($interestHeaders);

        foreach ($interestHeaders as $key => $value) {
            if (strpos($key, $constants::HEADER_PREFIX) === 0) {
                $buffer[] = $key . ':' . $value;
            } else {
                $buffer[] = $value;
            }
            $buffer[] = "\n";
        }

        $uri = '';

        $bucketName = $this->isCname ? $headers['Host'] : $bucketName;

        if ($bucketName) {
            $uri .= '/';
            $uri .= $bucketName;
            if (!$this->pathStyle) {
                $uri .= '/';
            }
        }

        if ($objectKey) {
            if (!($pos = strripos($uri, '/')) || strlen($uri) - 1 !== $pos) {
                $uri .= '/';
            }
            $uri .= $objectKey;
        }

        $buffer[] = $uri === '' ? '/' : $uri;


        if (!empty($pathArgs)) {
            ksort($pathArgs);
            $_pathArgs = [];
            foreach ($pathArgs as $key => $value) {
                if (in_array(strtolower($key), $constants::ALLOWED_RESOURCE_PARAMTER_NAMES) || strpos($key,
                        $constants::HEADER_PREFIX) === 0) {
                    $_pathArgs[] = $value === null || $value === '' ? $key : $key . '=' . urldecode($value);
                }
            }
            if (!empty($_pathArgs)) {
                $buffer[] = '?';
                $buffer[] = implode('&', $_pathArgs);
            }
        }

        return implode('', $buffer);
    }

}