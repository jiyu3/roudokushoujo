<?php

namespace WebPay\Data;

use WebPay\AbstractData;

class DeletedResponse extends AbstractData
{

    public function __construct(array $params)
    {
        $this->fields = array('deleted');
        $params = $this->normalize($this->fields, $params);
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        throw new \Exception('This class is immutable');
    }

}
