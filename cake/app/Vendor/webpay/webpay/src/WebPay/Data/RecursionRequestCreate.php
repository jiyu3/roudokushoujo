<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class RecursionRequestCreate extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof RecursionRequestCreate)) {
            return $params;
        }
        if (is_array($params)) {
            return new RecursionRequestCreate($params);
        }
        throw new InvalidRequestException('RecursionRequestCreate does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array('amount', 'currency', 'customer', 'period', 'description', 'shop', 'first_scheduled', 'uuid');
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
        $this->copyIfExists($this->attributes, $result, 'amount', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'currency', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'customer', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'period', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'description', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'shop', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'first_scheduled', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'uuid', 'requestBody');

        return $result;
    }

    public function queryParams()
    {
        $result = array();

        return $result;
    }
}
