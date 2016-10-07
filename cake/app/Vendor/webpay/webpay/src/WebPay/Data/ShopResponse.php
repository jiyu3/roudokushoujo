<?php

namespace WebPay\Data;

use WebPay\AbstractData;

class ShopResponse extends AbstractData
{

    public function __construct(array $params)
    {
        $this->fields = array('id', 'object', 'livemode', 'status', 'description', 'access_key', 'created', 'statement_descriptor', 'card_types_supported', 'details');
        $params = $this->normalize($this->fields, $params);
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        throw new \Exception('This class is immutable');
    }

}
