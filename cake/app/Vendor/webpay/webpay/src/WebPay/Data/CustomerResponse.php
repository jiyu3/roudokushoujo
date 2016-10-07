<?php

namespace WebPay\Data;

use WebPay\AbstractData;

class CustomerResponse extends AbstractData
{

    public function __construct(array $params)
    {
        $this->fields = array('id', 'object', 'livemode', 'created', 'active_card', 'description', 'email', 'recursions', 'deleted');
        $params = $this->normalize($this->fields, $params);
        $params['active_card'] = is_array($params['active_card']) ? new CardResponse($params['active_card']) : $params['active_card'];
        $params['recursions'] = is_array($params['recursions']) ? array_map(function ($x) { return is_array($x) ? new RecursionResponse($x) : $x; }, $params['recursions']) : $params['recursions'];
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
