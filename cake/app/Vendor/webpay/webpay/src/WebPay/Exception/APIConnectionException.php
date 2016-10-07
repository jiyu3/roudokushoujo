<?php

namespace WebPay\Exception;

class APIConnectionException extends WebPayException
{
    /** @var Exception */
    private $cause;

    /**
     * @param integer $status
     * @param array   $errorInfo
     */
    public function __construct($message, $status, $errorInfo, $cause)
    {
        parent::__construct($message, $status, $errorInfo);
        $this->cause = $cause;
    }

    /**
     * return Exception
     */
    public function getCause()
    {
        return $this->cause;
    }
}
