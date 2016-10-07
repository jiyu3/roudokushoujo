<?php

namespace WebPay\Data;

use WebPay\AbstractData;

class ErrorBody extends AbstractData
{

    public function __construct(array $params)
    {
        $this->fields = array('message', 'type', 'caused_by', 'code', 'param', 'charge');
        $params = $this->normalize($this->fields, $params);
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        throw new \Exception('This class is immutable');
    }

}
