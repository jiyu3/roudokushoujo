<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class CardRequest extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof CardRequest)) {
            return $params;
        }
        if (is_array($params)) {
            return new CardRequest($params);
        }
        throw new InvalidRequestException('CardRequest does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array('number', 'exp_month', 'exp_year', 'cvc', 'name');
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
        $this->copyIfExists($this->attributes, $result, 'number', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'exp_month', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'exp_year', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'cvc', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'name', 'requestBody');

        return $result;
    }

    public function queryParams()
    {
        $result = array();

        return $result;
    }
}
