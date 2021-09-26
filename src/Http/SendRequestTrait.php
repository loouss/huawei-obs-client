<?php

namespace Loouss\ObsClient\Http;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7;
use Loouss\ObsClient\Constant\ObsClientConst;
use Loouss\ObsClient\Exception\RuntimeException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Loouss\ObsClient\Http\Common\Model;
use Loouss\ObsClient\ObsException;
use Loouss\ObsClient\Signature\DefaultSignature;
use GuzzleHttp\Client;
use Loouss\ObsClient\Internal\Resource\Constants;
use Psr\Http\Message\StreamInterface;

trait SendRequestTrait
{
    protected string $ak;

    protected string $sk;

    protected bool $securityToken = false;

    protected string $endpoint = '';

    protected bool $pathStyle = false;

    protected string $region = 'region';

    protected string $signature = 'obs';

    protected bool $sslVerify = false;

    protected int $maxRetryCount = 3;

    protected int $timeout = 0;

    protected int $socketTimeout = 60;

    protected int $connectTimeout = 60;

    protected bool $isCname = false;

    protected Client $httpClient;

    public function createSignedUrl(array $args = []): Model
    {
        return $this->createCommonSignedUrl($args, $this->signature);
    }


    private function createCommonSignedUrl(array $args, $signature): Model
    {
        if (!isset($args['Method'])) {
            $obsException = new ObsException('Method param must be specified, allowed values: GET | PUT | HEAD | POST | DELETE | OPTIONS');
            $obsException->setExceptionType('client');
            throw $obsException;
        }
        $method = strval($args['Method']);
        $bucketName = isset($args['Bucket']) ? strval($args['Bucket']) : null;
        $objectKey = isset($args['Key']) ? strval($args['Key']) : null;
        $specialParam = isset($args['SpecialParam']) ? strval($args['SpecialParam']) : null;
        $expires = isset($args['Expires']) && is_numeric($args['Expires']) ? intval($args['Expires']) : 300;

        $headers = [];
        if (isset($args['Headers']) && is_array($args['Headers'])) {
            foreach ($args['Headers'] as $key => $val) {
                if (is_string($key) && $key !== '') {
                    $headers[$key] = $val;
                }
            }
        }


        $queryParams = [];
        if (isset($args['QueryParams']) && is_array($args['QueryParams'])) {
            foreach ($args['QueryParams'] as $key => $val) {
                if (is_string($key) && $key !== '') {
                    $queryParams[$key] = $val;
                }
            }
        }

        $constants = Constants::selectConstants($signature);
        if ($this->securityToken && !isset($queryParams[$constants::SECURITY_TOKEN_HEAD])) {
            $queryParams[$constants::SECURITY_TOKEN_HEAD] = $this->securityToken;
        }

        $sign = new DefaultSignature($this->ak, $this->sk, $this->pathStyle, $this->endpoint, $method, $this->signature,
            $this->securityToken, $this->isCname);

        $url = parse_url($this->endpoint);
        $host = $url['host'];

        $result = '';

        if ($bucketName) {
            if ($this->pathStyle) {
                $result = '/'.$bucketName;
            } else {
                $host = $this->isCname ? $host : $bucketName.'.'.$host;
            }
        }

        $headers['Host'] = $host;

        if ($objectKey) {
            $objectKey = $sign->urlencodeWithSafe($objectKey);
            $result .= '/'.$objectKey;
        }

        $result .= '?';

        if ($specialParam) {
            $queryParams[$specialParam] = '';
        }

        $queryParams[$constants::TEMPURL_AK_HEAD] = $this->ak;


        if (!is_numeric($expires) || $expires < 0) {
            $expires = 300;
        }
        $expires = intval($expires) + intval(microtime(true));

        $queryParams['Expires'] = strval($expires);

        $_queryParams = [];

        foreach ($queryParams as $key => $val) {
            $key = $sign->urlencodeWithSafe($key);
            $val = $sign->urlencodeWithSafe($val);
            $_queryParams[$key] = $val;
            $result .= $key;
            if ($val) {
                $result .= '='.$val;
            }
            $result .= '&';
        }

        $canonicalstring = $sign->makeCanonicalstring($method, $headers, $_queryParams, $bucketName, $objectKey,
            $expires);
        $signatureContent = base64_encode(hash_hmac('sha1', $canonicalstring, $this->sk, true));

        $result .= 'Signature='.$sign->urlencodeWithSafe($signatureContent);

        $model = new Model();
        $model['ActualSignedRequestHeaders'] = $headers;
        $model['SignedUrl'] = $url['scheme'].'://'.$host.':'.(isset($url['port']) ? $url['port'] : (strtolower($url['scheme']) === 'https' ? '443' : '80')).$result;
        return $model;
    }


    public function createPostSignature(array $args = []): Model
    {
        $bucketName = isset($args['Bucket']) ? strval($args['Bucket']) : null;
        $objectKey = isset($args['Key']) ? strval($args['Key']) : null;
        $expires = isset($args['Expires']) && is_numeric($args['Expires']) ? intval($args['Expires']) : 300;

        $formParams = [];

        if (isset($args['FormParams']) && is_array($args['FormParams'])) {
            foreach ($args['FormParams'] as $key => $val) {
                $formParams[$key] = $val;
            }
        }

        $constants = Constants::selectConstants($this->signature);
        if ($this->securityToken && !isset($formParams[$constants::SECURITY_TOKEN_HEAD])) {
            $formParams[$constants::SECURITY_TOKEN_HEAD] = $this->securityToken;
        }

        $timestamp = time();
        $expires = gmdate('Y-m-d\TH:i:s\Z', $timestamp + $expires);

        if ($bucketName) {
            $formParams['bucket'] = $bucketName;
        }

        if ($objectKey) {
            $formParams['key'] = $objectKey;
        }

        $policy = [];

        $policy[] = '{"expiration":"';
        $policy[] = $expires;
        $policy[] = '", "conditions":[';

        $matchAnyBucket = true;
        $matchAnyKey = true;

        $conditionAllowKeys = ['acl', 'bucket', 'key', 'success_action_redirect', 'redirect', 'success_action_status'];

        foreach ($formParams as $key => $val) {
            if ($key) {
                $key = strtolower(strval($key));

                if ($key === 'bucket') {
                    $matchAnyBucket = false;
                } else {
                    if ($key === 'key') {
                        $matchAnyKey = false;
                    }
                }

                if (!in_array($key, Constants::ALLOWED_REQUEST_HTTP_HEADER_METADATA_NAMES) && strpos($key,
                        $constants::HEADER_PREFIX) !== 0 && !in_array($key, $conditionAllowKeys)) {
                    $key = $constants::METADATA_PREFIX.$key;
                }

                $policy[] = '{"';
                $policy[] = $key;
                $policy[] = '":"';
                $policy[] = $val !== null ? strval($val) : '';
                $policy[] = '"},';
            }
        }

        if ($matchAnyBucket) {
            $policy[] = '["starts-with", "$bucket", ""],';
        }

        if ($matchAnyKey) {
            $policy[] = '["starts-with", "$key", ""],';
        }

        $policy[] = ']}';

        $originPolicy = implode('', $policy);

        $policy = base64_encode($originPolicy);

        $signatureContent = base64_encode(hash_hmac('sha1', $policy, $this->sk, true));

        $model = new Model();
        $model['OriginPolicy'] = $originPolicy;
        $model['Policy'] = $policy;
        $model['Signature'] = $signatureContent;
        return $model;
    }

    /**
     * @throws RuntimeException
     */
    public function __call($originMethod, $args)
    {
        $method = $originMethod;

        $contents = ObsClientConst::REQUEST_RESOURCE;

        $resource = $contents::$RESOURCE_ARRAY;

        if (isset($resource['aliases'][$method])) {
            $method = $resource['aliases'][$method];
        }

        $method = lcfirst($method);

        $operation = $resource['operations'][$method] ?? null;

        if (!$operation) {
            throw new RuntimeException('unknow method '.$originMethod);
        }

        $model = new Model();
        $model['method'] = $method;
        $params = empty($args) ? [] : $args[0];
        $this->checkMimeType($method, $params);
        $this->doRequest($model, $operation, $params);

        unset($model['method']);
        return $model;
    }

    private function checkMimeType($method, &$params)
    {
        // fix bug that guzzlehttp lib will add the content-type if not set
        if (($method === 'putObject' || $method === 'initiateMultipartUpload' || $method === 'uploadPart') && (!isset($params['ContentType']) || $params['ContentType'] === null)) {
            if (isset($params['Key'])) {
                $params['ContentType'] = Psr7\MimeType::fromFilename($params['Key']);
            }

            if ((!isset($params['ContentType']) || $params['ContentType'] === null) && isset($params['SourceFile'])) {
                $params['ContentType'] = Psr7\MimeType::fromFilename($params['SourceFile']);
            }

            if (!isset($params['ContentType']) || $params['ContentType'] === null) {
                $params['ContentType'] = 'binary/octet-stream';
            }
        }
    }

    protected function makeRequest(Model $model, &$operation, $params, $endpoint = null): Request
    {
        if ($endpoint === null) {
            $endpoint = $this->endpoint;
        }
        $signatureInterface = new DefaultSignature($this->ak, $this->sk, $this->pathStyle, $endpoint, $model['method'],
            $this->signature, $this->securityToken, $this->isCname);

        $authResult = $signatureInterface->doAuth($operation, $params, $model);
        $httpMethod = $authResult['method'];
        $authResult['headers']['User-Agent'] = self::defaultUserAgent();

        if ($model['method'] === 'putObject') {
            $model['ObjectURL'] = ['value' => $authResult['requestUrl']];
        }
        return new Request($httpMethod, $authResult['requestUrl'], $authResult['headers'], $authResult['body']);
    }


    protected function doRequest(Model $model, &$operation, $params, $endpoint = null)
    {
        $request = $this->makeRequest($model, $operation, $params, $endpoint);
        $this->sendRequest($model, $operation, $params, $request);
    }

    protected function sendRequest($model, &$operation, $params, $request, $requestCount = 1)
    {
        $start = microtime(true);
        $saveAsStream = false;
        if (isset($operation['stream']) && $operation['stream']) {
            $saveAsStream = isset($params['SaveAsStream']) ? $params['SaveAsStream'] : false;

            if (isset($params['SaveAsFile'])) {
                if ($saveAsStream) {
                    $obsException = new ObsException('SaveAsStream cannot be used with SaveAsFile together');
                    $obsException->setExceptionType('client');
                    throw $obsException;
                }
                $saveAsStream = true;
            }
            if (isset($params['FilePath'])) {
                if ($saveAsStream) {
                    $obsException = new ObsException('SaveAsStream cannot be used with FilePath together');
                    $obsException->setExceptionType('client');
                    throw $obsException;
                }
                $saveAsStream = true;
            }

            if (isset($params['SaveAsFile']) && isset($params['FilePath'])) {
                $obsException = new ObsException('SaveAsFile cannot be used with FilePath together');
                $obsException->setExceptionType('client');
                throw $obsException;
            }
        }

        try {
            $response = $this->httpClient->send($request, ['stream' => $saveAsStream]);

            $resolve = function (Response $response) use (
                $model,
                $operation,
                $params,
                $request,
                $requestCount,
                $start
            ) {
                $statusCode = $response->getStatusCode();
                $readable = isset($params['Body']) && ($params['Body'] instanceof StreamInterface || is_resource($params['Body']));
                if ($statusCode >= 300 && $statusCode < 400 && $statusCode !== 304 && !$readable && $requestCount <= $this->maxRetryCount) {
                    if ($location = $response->getHeaderLine('location')) {
                        $url = parse_url($this->endpoint);
                        $newUrl = parse_url($location);
                        $scheme = (isset($newUrl['scheme']) ? $newUrl['scheme'] : $url['scheme']);
                        $defaultPort = strtolower($scheme) === 'https' ? '443' : '80';
                        $this->doRequest($model, $operation, $params, $scheme.'://'.$newUrl['host'].
                            ':'.(isset($newUrl['port']) ? $newUrl['port'] : $defaultPort));
                        return;
                    }
                }
                $this->parseResponse($model, $request, $response, $operation);
            };
            $resolve($response);
        } catch (GuzzleException $e) {
            $reject = function (RequestException $exception) use (
                $model,
                $operation,
                $params,
                $request,
                $requestCount,
                $start
            ) {
                $message = null;
                if ($exception instanceof ConnectException) {
                    if ($requestCount <= $this->maxRetryCount) {
                        $this->sendRequest($model, $operation, $params, $request, $requestCount + 1);
                        return;
                    } else {
                        $message = 'Exceeded retry limitation, max retry count:'.$this->maxRetryCount.', error message:'.$exception->getMessage();
                    }
                }
                $this->parseException($model, $request, $exception, $message);
            };
            $reject($e);
        }
    }


}
