<?php

namespace WebPay\Exception;

class CardException extends WebPayException
{
    /** @var string */
    private $type;

    /**
     * @var string
     *
     * PHP's Exception class has $code, so that name is not available for this purpose.
     */
    private $cardErrorCode;

    /** @var string */
    private $param;

    /**
     * @param integer $status
     * @param array   $errorInfo
     */
    public function __construct($status, $errorInfo)
    {
        parent::__construct($errorInfo['message'], $status, $errorInfo);
        $this->type = $errorInfo['type'];
        $this->cardErrorCode = $errorInfo['code'];
        $this->param = array_key_exists('param', $errorInfo) ? $errorInfo['param'] : null;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getCardErrorCode()
    {
        return $this->cardErrorCode;
    }

    /**
     * @return string
     */
    public function getParam()
    {
        return $this->param;
    }
}
