<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class ChargeRequestWithAmount extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof ChargeRequestWithAmount)) {
            return $params;
        }
        if (is_array($params)) {
            return new ChargeRequestWithAmount($params);
        }
        if ((is_object($params) && $params instanceof ChargeResponse)) {
            return new ChargeRequestWithAmount(array('id' => $params->id));
        }
        if (is_string($params)) {
            return new ChargeRequestWithAmount(array('id' => $params));
        }
        throw new InvalidRequestException('ChargeRequestWithAmount does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array('id', 'amount');
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

        return $result;
    }

    public function queryParams()
    {
        $result = array();

        return $result;
    }
}
