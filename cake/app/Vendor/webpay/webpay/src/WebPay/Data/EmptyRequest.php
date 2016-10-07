<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class EmptyRequest extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof EmptyRequest)) {
            return $params;
        }
        if (is_array($params)) {
            return new EmptyRequest($params);
        }
        throw new InvalidRequestException('EmptyRequest does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array();
        $params = $this->normalize($this->fields, $params);
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        $underscore = $this->decamelize($key);
        $this->attributes[$underscore] = $value;
    }

    public function requestBody()
    {
        $result = array();

        return $result;
    }

    public function queryParams()
    {
        $result = array();

        return $result;
    }
}
