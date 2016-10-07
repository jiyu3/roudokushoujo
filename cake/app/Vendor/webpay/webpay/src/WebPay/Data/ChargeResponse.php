<?php

namespace WebPay\Data;

use WebPay\AbstractData;

class ChargeResponse extends AbstractData
{

    public function __construct(array $params)
    {
        $this->fields = array('id', 'object', 'livemode', 'amount', 'card', 'created', 'currency', 'paid', 'captured', 'refunded', 'amount_refunded', 'customer', 'recursion', 'shop', 'description', 'failure_message', 'expire_time', 'fees');
        $params = $this->normalize($this->fields, $params);
        $params['card'] = is_array($params['card']) ? new CardResponse($params['card']) : $params['card'];
        $params['fees'] = is_array($params['fees']) ? array_map(function ($x) { return is_array($x) ? new ChargeFeeResponse($x) : $x; }, $params['fees']) : $params['fees'];
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        throw new \Exception('This class is immutable');
    }

}
