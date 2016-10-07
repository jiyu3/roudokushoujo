<?php

namespace WebPay;

class ApiConnectionException extends ApiException
{
    public function __construct($message, $previous)
    {
        parent::__construct($message, $previous);
    }

    public static function inRequest($exception)
    {
        return new ApiConnectionException("API request failed with " . $exception->getMessage(), $exception);
    }

    public static function invalidJson($exception)
    {
        return new ApiConnectionException("Server responded invalid JSON string", $exception);
    }
}
