<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class CustomerIdRequest extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof CustomerIdRequest)) {
            return $params;
        }
        if (is_array($params)) {
            return new CustomerIdRequest($params);
        }
        if ((is_object($params) && $params instanceof CustomerResponse)) {
            return new CustomerIdRequest(array('id' => $params->id));
        }
        if (is_string($params)) {
            return new CustomerIdRequest(array('id' => $params));
        }
        throw new InvalidRequestException('CustomerIdRequest does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array('id');
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
