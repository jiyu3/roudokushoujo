<?php

namespace WebPay\ErrorResponse;

use WebPay\Data\ErrorData;

class AuthenticationException extends ErrorResponseException
{
    public function __construct($status, array $rawData)
    {
        $data = new ErrorData($rawData);
        $message = sprintf('%s: %s', 'AuthenticationException', $data->error->message);
        parent::__construct($message, $status, $data);
    }
}
