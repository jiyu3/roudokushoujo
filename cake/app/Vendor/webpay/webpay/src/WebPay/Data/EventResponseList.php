<?php

namespace WebPay\Data;

use WebPay\AbstractData;

class EventResponseList extends AbstractData
{

    public function __construct(array $params)
    {
        $this->fields = array('object', 'url', 'count', 'data');
        $params = $this->normalize($this->fields, $params);
        $params['data'] = is_array($params['data']) ? array_map(function ($x) { return is_array($x) ? new EventResponse($x) : $x; }, $params['data']) : $params['data'];
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        throw new \Exception('This class is immutable');
    }

}
