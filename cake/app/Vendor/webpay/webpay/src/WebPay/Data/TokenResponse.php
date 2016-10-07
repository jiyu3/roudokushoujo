<?php

namespace WebPay\Data;

use WebPay\AbstractData;

class TokenResponse extends AbstractData
{

    public function __construct(array $params)
    {
        $this->fields = array('id', 'object', 'livemode', 'card', 'created', 'used');
        $params = $this->normalize($this->fields, $params);
        $params['card'] = is_array($params['card']) ? new CardResponse($params['card']) : $params['card'];
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        throw new \Exception('This class is immutable');
    }

}
