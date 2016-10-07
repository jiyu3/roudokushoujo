<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class EventListRequest extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof EventListRequest)) {
            return $params;
        }
        if (is_array($params)) {
            return new EventListRequest($params);
        }
        throw new InvalidRequestException('EventListRequest does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array('count', 'offset', 'created', 'type', 'shop');
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

    public function requestBody()
    {
        $result = array();

        return $result;
    }

    public function queryParams()
    {
        $result = array();
        $this->copyIfExists($this->attributes, $result, 'count', 'queryParams');
        $this->copyIfExists($this->attributes, $result, 'offset', 'queryParams');
        $this->copyIfExists($this->attributes, $result, 'created', 'queryParams');
        $this->copyIfExists($this->attributes, $result, 'type', 'queryParams');
        $this->copyIfExists($this->attributes, $result, 'shop', 'queryParams');

        return $result;
    }
}
