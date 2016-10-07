<?php

namespace WebPay\Exception;

class APIException extends WebPayException
{
    /** @var string */
    private $type;

    /**
     * @param integer $status
     * @param array   $errorInfo
     */
    public function __construct($status, $errorInfo)
    {
        parent::__construct($errorInfo['message'], $status, $errorInfo);
        $this->type = $errorInfo['type'];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
