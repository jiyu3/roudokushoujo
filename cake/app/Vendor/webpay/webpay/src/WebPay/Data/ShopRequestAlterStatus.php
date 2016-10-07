<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class ShopRequestAlterStatus extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof ShopRequestAlterStatus)) {
            return $params;
        }
        if (is_array($params)) {
            return new ShopRequestAlterStatus($params);
        }
        if ((is_object($params) && $params instanceof ShopResponse)) {
            return new ShopRequestAlterStatus(array('id' => $params->id));
        }
        if (is_string($params)) {
            return new ShopRequestAlterStatus(array('id' => $params));
        }
        throw new InvalidRequestException('ShopRequestAlterStatus does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array('id', 'status');
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
        $this->copyIfExists($this->attributes, $result, 'status', 'requestBody');

        return $result;
    }

    public function queryParams()
    {
        $result = array();

        return $result;
    }
}
