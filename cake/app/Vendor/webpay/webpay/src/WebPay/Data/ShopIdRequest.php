<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class ShopIdRequest extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof ShopIdRequest)) {
            return $params;
        }
        if (is_array($params)) {
            return new ShopIdRequest($params);
        }
        if ((is_object($params) && $params instanceof ShopResponse)) {
            return new ShopIdRequest(array('id' => $params->id));
        }
        if (is_string($params)) {
            return new ShopIdRequest(array('id' => $params));
        }
        throw new InvalidRequestException('ShopIdRequest does not accept the given value', $params);
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
