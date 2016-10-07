<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class ChargeIdRequest extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof ChargeIdRequest)) {
            return $params;
        }
        if (is_array($params)) {
            return new ChargeIdRequest($params);
        }
        if ((is_object($params) && $params instanceof ChargeResponse)) {
            return new ChargeIdRequest(array('id' => $params->id));
        }
        if (is_string($params)) {
            return new ChargeIdRequest(array('id' => $params));
        }
        throw new InvalidRequestException('ChargeIdRequest does not accept the given value', $params);
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
