<?php

namespace WebPay\Data;

use WebPay\AbstractData;

class RecursionResponse extends AbstractData
{

    public function __construct(array $params)
    {
        $this->fields = array('id', 'object', 'livemode', 'created', 'amount', 'currency', 'period', 'description', 'customer', 'shop', 'last_executed', 'next_scheduled', 'status', 'deleted');
        $params = $this->normalize($this->fields, $params);
        if (!array_key_exists('deleted', $params) || $params['deleted'] === null) {
          $params['deleted'] = false;
}
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        throw new \Exception('This class is immutable');
    }

}
