<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class CreatedRange extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof CreatedRange)) {
            return $params;
        }
        if (is_array($params)) {
            return new CreatedRange($params);
        }
        throw new InvalidRequestException('CreatedRange does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array('gt', 'gte', 'lt', 'lte');
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
        $this->copyIfExists($this->attributes, $result, 'gt', 'queryParams');
        $this->copyIfExists($this->attributes, $result, 'gte', 'queryParams');
        $this->copyIfExists($this->attributes, $result, 'lt', 'queryParams');
        $this->copyIfExists($this->attributes, $result, 'lte', 'queryParams');

        return $result;
    }
}
