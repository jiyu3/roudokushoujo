<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class ChargeRequestRefund extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof ChargeRequestRefund)) {
            return $params;
        }
        if (is_array($params)) {
            return new ChargeRequestRefund($params);
        }
        if ((is_object($params) && $params instanceof ChargeResponse)) {
            return new ChargeRequestRefund(array('id' => $params->id));
        }
        if (is_string($params)) {
            return new ChargeRequestRefund(array('id' => $params));
        }
        throw new InvalidRequestException('ChargeRequestRefund does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array('id', 'amount', 'uuid');
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
        $this->copyIfExists($this->attributes, $result, 'uuid', 'requestBody');

        return $result;
    }

    public function queryParams()
    {
        $result = array();

        return $result;
    }
}
