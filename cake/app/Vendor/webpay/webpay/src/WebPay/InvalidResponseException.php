<?php

namespace WebPay;

class InvalidResponseException extends ApiException
{
    private $badValue;

    public function __construct($message, $badValue)
    {
        parent::__construct($message);
        $this->badValue = $badValue;
    }

    public function getBadValue()
    {
        return $this->badValue;
    }
}
