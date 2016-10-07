<?php

namespace WebPay\Data;

use WebPay\AbstractData;

class AccountResponse extends AbstractData
{

    public function __construct(array $params)
    {
        $this->fields = array('id', 'object', 'charge_enabled', 'currencies_supported', 'details_submitted', 'email', 'statement_descriptor', 'card_types_supported');
        $params = $this->normalize($this->fields, $params);
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        throw new \Exception('This class is immutable');
    }

}
