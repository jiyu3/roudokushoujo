<?php

namespace WebPay\Exception;

class AuthenticationException extends WebPayException
{
    /**
     * @param integer $status
     * @param array   $errorInfo
     */
    public function __construct($status, $errorInfo)
    {
        parent::__construct($errorInfo['message'], $status, $errorInfo);
    }
}
