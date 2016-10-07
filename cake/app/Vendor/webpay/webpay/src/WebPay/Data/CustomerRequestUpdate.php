<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class CustomerRequestUpdate extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof CustomerRequestUpdate)) {
            return $params;
        }
        if (is_array($params)) {
            return new CustomerRequestUpdate($params);
        }
        if ((is_object($params) && $params instanceof CustomerResponse)) {
            return new CustomerRequestUpdate(array('id' => $params->id));
        }
        if (is_string($params)) {
            return new CustomerRequestUpdate(array('id' => $params));
        }
        throw new InvalidRequestException('CustomerRequestUpdate does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array('id', 'card', 'description', 'email');
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

        return $result;
    }

    public function queryParams()
    {
        $result = array();

        return $result;
    }
}
