<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class CustomerRequestCreate extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof CustomerRequestCreate)) {
            return $params;
        }
        if (is_array($params)) {
            return new CustomerRequestCreate($params);
        }
        throw new InvalidRequestException('CustomerRequestCreate does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array('card', 'description', 'email', 'uuid');
        $params = $this->normalize($this->fields, $params);
        $params['card'] = is_array($params['card']) ? new CardRequest($params['card']) : $params['card'];
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        $underscore = $this->decamelize($key);
        if ($underscore === 'card') { $value = is_array($value) ? new CardRequest($value) : $value; }
        $this->attributes[$underscore] = $value;
    }

    public function requestBody()
    {
        $result = array();
        $this->copyIfExists($this->attributes, $result, 'card', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'description', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'email', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'uuid', 'requestBody');

        return $result;
    }

    public function queryParams()
    {
        $result = array();

        return $result;
    }
}
