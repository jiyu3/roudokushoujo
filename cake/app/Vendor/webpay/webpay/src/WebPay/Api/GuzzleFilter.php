<?php

namespace WebPay\Api;

/**
 * Static filter methods for Guzzle's service description
 */
abstract class GuzzleFilter
{
    /**
     * @param boolean
     * @return string
     */
    public static function booleanToStringFilter($value)
    {
        if ($value === true)
            return "true";
        else
            return "false";
    }
}
