<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class ShopRequestUpdate extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof ShopRequestUpdate)) {
            return $params;
        }
        if (is_array($params)) {
            return new ShopRequestUpdate($params);
        }
        if ((is_object($params) && $params instanceof ShopResponse)) {
            return new ShopRequestUpdate(array('id' => $params->id));
        }
        if (is_string($params)) {
            return new ShopRequestUpdate(array('id' => $params));
        }
        throw new InvalidRequestException('ShopRequestUpdate does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array('id', 'description', 'details');
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
        $this->copyIfExists($this->attributes, $result, 'description', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'details', 'requestBody');

        return $result;
    }

    public function queryParams()
    {
        $result = array();

        return $result;
    }
}
