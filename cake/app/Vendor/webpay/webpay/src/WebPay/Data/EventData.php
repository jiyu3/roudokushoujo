<?php

namespace WebPay\Data;

use WebPay\AbstractData;

class EventData extends AbstractData
{

    public function __construct(array $params)
    {
        $this->fields = array('object', 'previous_attributes');
        $params = $this->normalize($this->fields, $params);
        if (!is_array($params['object']) || !array_key_exists('object', $params['object'])) {
    $params['object'] = $params['object'];
} else {
    switch ($params['object']['object']) {
        case 'charge':
            $params['object'] = new \WebPay\Data\ChargeResponse($params['object']);
            break;
        case 'customer':
            $params['object'] = new \WebPay\Data\CustomerResponse($params['object']);
            break;
        case 'shop':
            $params['object'] = new \WebPay\Data\ShopResponse($params['object']);
            break;
        case 'recursion':
            $params['object'] = new \WebPay\Data\RecursionResponse($params['object']);
            break;
        case 'account':
            $params['object'] = new \WebPay\Data\AccountResponse($params['object']);
            break;
        default:
            $params['object'] = $params['object'];
            break;
    }
};
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        throw new \Exception('This class is immutable');
    }

}
