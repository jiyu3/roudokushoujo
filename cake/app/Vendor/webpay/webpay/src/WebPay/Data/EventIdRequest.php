<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class EventIdRequest extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof EventIdRequest)) {
            return $params;
        }
        if (is_array($params)) {
            return new EventIdRequest($params);
        }
        if ((is_object($params) && $params instanceof EventResponse)) {
            return new EventIdRequest(array('id' => $params->id));
        }
        if (is_string($params)) {
            return new EventIdRequest(array('id' => $params));
        }
        throw new InvalidRequestException('EventIdRequest does not accept the given value', $params);
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
