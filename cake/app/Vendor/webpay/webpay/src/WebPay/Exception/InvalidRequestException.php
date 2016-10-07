<?php

namespace WebPay\Exception;

class InvalidRequestException extends WebPayException
{
    /** @var string */
    private $type;

    /** @var string */
    private $param;

    /**
     * Create a fake InvalidRequestException to indicate the given id is invalid
     *
     * @return InvalidRequestException
     */
    public static function emptyIdException()
    {
        return new self(null, array(
            'message' => 'id must not be empty',
            'type' => 'invalid_request_error',
            'param' => 'id'
        ));
    }

    /**
     * @param integer $status
     * @param array   $errorInfo
     */
    public function __construct($status, $errorInfo)
    {
        parent::__construct($errorInfo['message'], $status, $errorInfo);
        $this->type = $errorInfo['type'];
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
    public function getParam()
    {
        return $this->param;
    }
}
