<?php

namespace WebPay\Data;

use WebPay\AbstractData;

class ChargeFeeResponse extends AbstractData
{

    public function __construct(array $params)
    {
        $this->fields = array('object', 'transaction_type', 'transaction_fee', 'rate', 'amount', 'created');
        $params = $this->normalize($this->fields, $params);
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        throw new \Exception('This class is immutable');
    }

}
