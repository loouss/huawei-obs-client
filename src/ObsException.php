<?php


namespace Loouss\ObsClient;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Loouss\ObsClient\Log\ObsLog;

class ObsException extends \RuntimeException
{
    const CLIENT = 'client';

    const SERVER = 'server';

    private $response;

    private $request;

    private $requestId;

    private $exceptionType;

    private $exceptionCode;

    private $exceptionMessage;

    private $hostId;

    public function __construct($message = null, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function setExceptionCode($exceptionCode)
    {
        $this->exceptionCode = $exceptionCode;
    }

    public function getExceptionCode()
    {
        return $this->exceptionCode;
    }

    public function setExceptionMessage($exceptionMessage)
    {
        $this->exceptionMessage = $exceptionMessage;
    }

    public function getExceptionMessage()
    {
        return $this->exceptionMessage ? $this->exceptionMessage : $this->message;
    }

    public function setExceptionType($exceptionType)
    {
        $this->exceptionType = $exceptionType;
    }

    public function getExceptionType()
    {
        return $this->exceptionType;
    }

    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getStatusCode()
    {
        return $this->response ? $this->response->getStatusCode() : -1;
    }

    public function setHostId($hostId)
    {
        $this->hostId = $hostId;
    }

    public function getHostId()
    {
        return $this->hostId;
    }

    public function __toString()
    {
        $message = get_class($this).': '
            .'OBS Error Code: '.$this->getExceptionCode().', '
            .'Status Code: '.$this->getStatusCode().', '
            .'OBS Error Type: '.$this->getExceptionType().', '
            .'OBS Error Message: '.($this->getExceptionMessage() ? $this->getExceptionMessage() : $this->getMessage());

        // Add the User-Agent if available
        if ($this->request) {
            $message .= ', '.'User-Agent: '.$this->request->getHeaderLine('User-Agent');
        }
        $message .= "\n";

        return $message;
    }

}
