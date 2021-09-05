<?php

namespace Loouss\ObsClient;

use Loouss\ObsClient\Internal\Common\Model;
use Monolog\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\Promise;


define('DEBUG', Logger::DEBUG);
define('INFO', Logger::INFO);
define('NOTICE', Logger::NOTICE);
define('WARNING', Logger::WARNING);
define('WARN', Logger::WARNING);
define('ERROR', Logger::ERROR);
define('CRITICAL', Logger::CRITICAL);
define('ALERT', Logger::ALERT);
define('EMERGENCY', Logger::EMERGENCY);

/**
 * @method Model createPostSignature(array $args = []);
 * @method Model createSignedUrl(array $args = []);
 * @method Model createBucket(array $args = []);
 * @method Model listBuckets();
 * @method Model deleteBucket(array $args = []);
 * @method Model listObjects(array $args = []);
 * @method Model listVersions(array $args = []);
 * @method Model headBucket(array $args = []);
 * @method Model getBucketMetadata(array $args = []);
 * @method Model getBucketLocation(array $args = []);
 * @method Model getBucketStorageInfo(array $args = []);
 * @method Model setBucketQuota(array $args = []);
 * @method Model getBucketQuota(array $args = []);
 * @method Model setBucketStoragePolicy(array $args = []);
 * @method Model getBucketStoragePolicy(array $args = []);
 * @method Model setBucketAcl(array $args = []);
 * @method Model getBucketAcl(array $args = []);
 * @method Model setBucketLogging(array $args = []);
 * @method Model getBucketLogging(array $args = []);
 * @method Model setBucketPolicy(array $args = []);
 * @method Model getBucketPolicy(array $args = []);
 * @method Model deleteBucketPolicy(array $args = []);
 * @method Model setBucketLifecycle(array $args = []);
 * @method Model getBucketLifecycle(array $args = []);
 * @method Model deleteBucketLifecycle(array $args = []);
 * @method Model setBucketWebsite(array $args = []);
 * @method Model getBucketWebsite(array $args = []);
 * @method Model deleteBucketWebsite(array $args = []);
 * @method Model setBucketVersioning(array $args = []);
 * @method Model getBucketVersioning(array $args = []);
 * @method Model setBucketCors(array $args = []);
 * @method Model getBucketCors(array $args = []);
 * @method Model deleteBucketCors(array $args = []);
 * @method Model setBucketNotification(array $args = []);
 * @method Model getBucketNotification(array $args = []);
 * @method Model setBucketTagging(array $args = []);
 * @method Model getBucketTagging(array $args = []);
 * @method Model deleteBucketTagging(array $args = []);
 * @method Model optionsBucket(array $args = []);
 * @method Model getFetchPolicy(array $args = []);
 * @method Model setFetchPolicy(array $args = []);
 * @method Model deleteFetchPolicy(array $args = []);
 * @method Model setFetchJob(array $args = []);
 * @method Model getFetchJob(array $args = []);
 *
 * @method Model putObject(array $args = []);
 * @method Model getObject(array $args = []);
 * @method Model copyObject(array $args = []);
 * @method Model deleteObject(array $args = []);
 * @method Model deleteObjects(array $args = []);
 * @method Model getObjectMetadata(array $args = []);
 * @method Model setObjectAcl(array $args = []);
 * @method Model getObjectAcl(array $args = []);
 * @method Model initiateMultipartUpload(array $args = []);
 * @method Model uploadPart(array $args = []);
 * @method Model copyPart(array $args = []);
 * @method Model listParts(array $args = []);
 * @method Model completeMultipartUpload(array $args = []);
 * @method Model abortMultipartUpload(array $args = []);
 * @method Model listMultipartUploads(array $args = []);
 * @method Model optionsObject(array $args = []);
 * @method Model restoreObject(array $args = []);
 *
 * @method Promise createBucketAsync(array $args = [], callable $callback);
 * @method Promise listBucketsAsync(callable $callback);
 * @method Promise deleteBucketAsync(array $args = [], callable $callback);
 * @method Promise listObjectsAsync(array $args = [], callable $callback);
 * @method Promise listVersionsAsync(array $args = [], callable $callback);
 * @method Promise headBucketAsync(array $args = [], callable $callback);
 * @method Promise getBucketMetadataAsync(array $args = [], callable $callback);
 * @method Promise getBucketLocationAsync(array $args = [], callable $callback);
 * @method Promise getBucketStorageInfoAsync(array $args = [], callable $callback);
 * @method Promise setBucketQuotaAsync(array $args = [], callable $callback);
 * @method Promise getBucketQuotaAsync(array $args = [], callable $callback);
 * @method Promise setBucketStoragePolicyAsync(array $args = [], callable $callback);
 * @method Promise getBucketStoragePolicyAsync(array $args = [], callable $callback);
 * @method Promise setBucketAclAsync(array $args = [], callable $callback);
 * @method Promise getBucketAclAsync(array $args = [], callable $callback);
 * @method Promise setBucketLoggingAsync(array $args = [], callable $callback);
 * @method Promise getBucketLoggingAsync(array $args = [], callable $callback);
 * @method Promise setBucketPolicyAsync(array $args = [], callable $callback);
 * @method Promise getBucketPolicyAsync(array $args = [], callable $callback);
 * @method Promise deleteBucketPolicyAsync(array $args = [], callable $callback);
 * @method Promise setBucketLifecycleAsync(array $args = [], callable $callback);
 * @method Promise getBucketLifecycleAsync(array $args = [], callable $callback);
 * @method Promise deleteBucketLifecycleAsync(array $args = [], callable $callback);
 * @method Promise setBucketWebsiteAsync(array $args = [], callable $callback);
 * @method Promise getBucketWebsiteAsync(array $args = [], callable $callback);
 * @method Promise deleteBucketWebsiteAsync(array $args = [], callable $callback);
 * @method Promise setBucketVersioningAsync(array $args = [], callable $callback);
 * @method Promise getBucketVersioningAsync(array $args = [], callable $callback);
 * @method Promise setBucketCorsAsync(array $args = [], callable $callback);
 * @method Promise getBucketCorsAsync(array $args = [], callable $callback);
 * @method Promise deleteBucketCorsAsync(array $args = [], callable $callback);
 * @method Promise setBucketNotificationAsync(array $args = [], callable $callback);
 * @method Promise getBucketNotificationAsync(array $args = [], callable $callback);
 * @method Promise setBucketTaggingAsync(array $args = [], callable $callback);
 * @method Promise getBucketTaggingAsync(array $args = [], callable $callback);
 * @method Promise deleteBucketTaggingAsync(array $args = [], callable $callback);
 * @method Promise optionsBucketAsync(array $args = [], callable $callback);
 *
 * @method Promise putObjectAsync(array $args = [], callable $callback);
 * @method Promise getObjectAsync(array $args = [], callable $callback);
 * @method Promise copyObjectAsync(array $args = [], callable $callback);
 * @method Promise deleteObjectAsync(array $args = [], callable $callback);
 * @method Promise deleteObjectsAsync(array $args = [], callable $callback);
 * @method Promise getObjectMetadataAsync(array $args = [], callable $callback);
 * @method Promise setObjectAclAsync(array $args = [], callable $callback);
 * @method Promise getObjectAclAsync(array $args = [], callable $callback);
 * @method Promise initiateMultipartUploadAsync(array $args = [], callable $callback);
 * @method Promise uploadPartAsync(array $args = [], callable $callback);
 * @method Promise copyPartAsync(array $args = [], callable $callback);
 * @method Promise listPartsAsync(array $args = [], callable $callback);
 * @method Promise completeMultipartUploadAsync(array $args = [], callable $callback);
 * @method Promise abortMultipartUploadAsync(array $args = [], callable $callback);
 * @method Promise listMultipartUploadsAsync(array $args = [], callable $callback);
 * @method Promise optionsObjectAsync(array $args = [], callable $callback);
 * @method Promise restoreObjectAsync(array $args = [], callable $callback);
 * @method Model getFetchPolicyAsync(array $args = [], callable $callback);
 * @method Model setFetchPolicyAsync(array $args = [], callable $callback);
 * @method Model deleteFetchPolicyAsync(array $args = [], callable $callback);
 * @method Model setFetchJobAsync(array $args = [], callable $callback);
 * @method Model getFetchJobAsync(array $args = [], callable $callback);
 *
 */
class ObsClient
{


    use Internal\SendRequestTrait;
    use Internal\GetResponseTrait;

    private array $factorys;

    protected ?HandlerStack $handlerStack = null;

    public function __construct(string $ak, string $sk, string $endpoint, array $config = [])
    {
        $this->factorys = [];

        $this->ak = $ak;
        $this->sk = $sk;


        if (isset($config['security_token'])) {
            $this->securityToken = strval($config['security_token']);
        }


        $this->endpoint = $endpoint;

        while ($this->endpoint[strlen($this->endpoint) - 1] === '/') {
            $this->endpoint = substr($this->endpoint, 0, strlen($this->endpoint) - 1);
        }

        if (strpos($this->endpoint, 'http') !== 0) {
            $this->endpoint = 'https://' . $this->endpoint;
        }

        if (isset($config['path_style'])) {
            $this->pathStyle = $config['path_style'];
        }

        if (isset($config['region'])) {
            $this->region = strval($config['region']);
        }

        if (isset($config['ssl_verify'])) {
            $this->sslVerify = $config['ssl_verify'];
        } else {
            if (isset($config['ssl.certificate_authority'])) {
                $this->sslVerify = $config['ssl.certificate_authority'];
            }
        }

        if (isset($config['max_retry_count'])) {
            $this->maxRetryCount = intval($config['max_retry_count']);
        }

        if (isset($config['timeout'])) {
            $this->timeout = intval($config['timeout']);
        }

        if (isset($config['socket_timeout'])) {
            $this->socketTimeout = intval($config['socket_timeout']);
        }

        if (isset($config['connect_timeout'])) {
            $this->connectTimeout = intval($config['connect_timeout']);
        }

        if (isset($config['chunk_size'])) {
            $this->chunkSize = intval($config['chunk_size']);
        }

        if (isset($config['exception_response_mode'])) {
            $this->exceptionResponseMode = $config['exception_response_mode'];
        }

        if (isset($config['is_cname'])) {
            $this->isCname = $config['is_cname'];
        }

        $host = parse_url($this->endpoint)['host'];
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            $this->pathStyle = true;
        }

        $this->httpClient = new Client(
            [
                'timeout' => 0,
                'read_timeout' => $this->socketTimeout,
                'connect_timeout' => $this->connectTimeout,
                'allow_redirects' => false,
                'verify' => $this->sslVerify,
                'expect' => false,
                'handler' => $this->getHandlerStack(),
            ]
        );
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * @param HandlerStack $handlerStack
     * @return $this
     */
    public function setHandlerStack(HandlerStack $handlerStack): ObsClient
    {
        $this->handlerStack = $handlerStack;

        return $this;
    }

    /**
     * Build a handler stack.
     *
     * @return \GuzzleHttp\HandlerStack
     */
    public function getHandlerStack(): HandlerStack
    {
        if ($this->handlerStack) {
            return $this->handlerStack;
        }

        $this->handlerStack = HandlerStack::create();

        return $this->handlerStack;
    }

    public function refresh($key, $secret, $security_token = false)
    {
        $this->ak = strval($key);
        $this->sk = strval($secret);
        if ($security_token) {
            $this->securityToken = strval($security_token);
        }
    }

    /**
     * @return string
     */
    private static function defaultUserAgent(): string
    {
        static $defaultAgent = '';
        if (!$defaultAgent) {
            $defaultAgent = 'obs-sdk-php/';
        }

        return $defaultAgent;
    }

    /**
     * Factory method to create a new src client using an array of configuration options.
     *
     * @param array $config Client configuration data
     *
     * @return ObsClient
     */
    public static function factory(array $config = []): ObsClient
    {
        return new ObsClient($config);
    }

    public function close()
    {
        if ($this->factorys) {
            foreach ($this->factorys as $factory) {
                $factory->close();
            }
        }
    }


}
