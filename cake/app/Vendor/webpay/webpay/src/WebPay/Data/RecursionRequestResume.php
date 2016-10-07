<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class RecursionRequestResume extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof RecursionRequestResume)) {
            return $params;
        }
        if (is_array($params)) {
            return new RecursionRequestResume($params);
        }
        if ((is_object($params) && $params instanceof RecursionResponse)) {
            return new RecursionRequestResume(array('id' => $params->id));
        }
        if (is_string($params)) {
            return new RecursionRequestResume(array('id' => $params));
        }
        throw new InvalidRequestException('RecursionRequestResume does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array('id', 'retry');
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
        $this->copyIfExists($this->attributes, $result, 'retry', 'requestBody');

        return $result;
    }

    public function queryParams()
    {
        $result = array();

        return $result;
    }
}
