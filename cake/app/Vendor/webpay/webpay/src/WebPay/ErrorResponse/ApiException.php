<?php

namespace WebPay\ErrorResponse;

use WebPay\Data\ErrorData;

class ApiException extends ErrorResponseException
{
    public function __construct($status, array $rawData)
    {
        $data = new ErrorData($rawData);
        $message = sprintf('%s: %s', 'ApiException', $data->error->message);
        parent::__construct($message, $status, $data);
    }
}
