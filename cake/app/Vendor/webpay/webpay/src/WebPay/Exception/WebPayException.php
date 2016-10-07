<?php

namespace WebPay\Exception;

class WebPayException extends \Exception
{
    /** @var integer */
    private $status;

    /** @var array */
    private $errorInfo;

    /**
     * @param string  $message
     * @param integer $status
     * @param array   $errorInfo
     */
    public function __construct($message, $status = null, $errorInfo = null)
    {
        parent::__construct($message);
        $this->status = $status;
        $this->errorInfo = $errorInfo;
    }

    /**
     * Create Exception object from response
     *
     * @param  \Guzzle\Http\Message\Response $response
     * @return WebPayException
     */
    public static function exceptionFromResponse(\Guzzle\Http\Message\Response $response)
    {
        $status = $response->getStatusCode();
        $data = $response->json();
        $errorInfo = isset($data['error']) ? $data['error'] : null;

        switch ($status) {
        case 400:
        case 404:
            return new InvalidRequestException($status, $errorInfo);
        case 401:
            return new AuthenticationException($status, $errorInfo);
        case 402:
            return new CardException($status, $errorInfo);
        default:
            return new APIException($status, $errorInfo);
        }
    }

    /**
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getErrorInfo()
    {
        return $this->errorInfo;
    }
}
