<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;
use WebPay\Data\CreatedRange;

class ListRequestWithEventType extends AbstractData {

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof ListRequestWithEventType)) {
            return $params;
        }
        if (is_array($params)) {
            return new ListRequestWithEventType($params);
        }
        throw new InvalidRequestException('ListRequestWithEventType does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array('count', 'offset', 'created', 'type');
        $params = $this->normalize($this->fields, $params);
        $params['created'] = is_array($params['created']) ? new CreatedRange($params['created']) : $params['created'];
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        $underscore = $this->decamelize($key);
        if ($underscore === 'created') { $value = is_array($value) ? new CreatedRange($value) : $value; }
        $this->attributes[$underscore] = $value;
    }
}
