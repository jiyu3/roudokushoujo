<?php

namespace WebPay;

class ApiException extends \Exception
{
    public function __construct($message, \Exception $previous = NULL)
    {
        parent::__construct($message, 0, $previous);
    }
}
