<?php

namespace WebPay\Data;

use WebPay\AbstractData;

class ErrorData extends AbstractData
{

    public function __construct(array $params)
    {
        $this->fields = array('error');
        $params = $this->normalize($this->fields, $params);
        $params['error'] = is_array($params['error']) ? new ErrorBody($params['error']) : $params['error'];
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        throw new \Exception('This class is immutable');
    }

}
