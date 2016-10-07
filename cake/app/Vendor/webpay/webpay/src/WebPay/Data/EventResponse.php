<?php

namespace WebPay\Data;

use WebPay\AbstractData;

class EventResponse extends AbstractData
{

    public function __construct(array $params)
    {
        $this->fields = array('id', 'object', 'livemode', 'created', 'data', 'pending_webhooks', 'type', 'shop');
        $params = $this->normalize($this->fields, $params);
        $params['data'] = is_array($params['data']) ? new EventData($params['data']) : $params['data'];
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        throw new \Exception('This class is immutable');
    }

}
