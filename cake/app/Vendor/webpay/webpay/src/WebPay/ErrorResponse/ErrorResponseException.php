<?php

namespace WebPay\ErrorResponse;

use WebPay\Data\ErrorData;
use WebPay\ApiException;

class ErrorResponseException extends ApiException
{
    /** var integer */
    private $status;
    /** var ErrorData */
    private $data;

    public function __construct($message, $status, ErrorData $data)
    {
        parent::__construct($message);
        $this->status = $status;
        $this->data = $data;
    }

    public function __get($key)
    {
        switch ($key) {
            case 'status':
                return $this->status;
            case 'data':
                return $this->data;
            default:
                throw new \Exception('Undefined field ' . $key);
        }
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getData()
    {
        return $this->data;
    }
}
