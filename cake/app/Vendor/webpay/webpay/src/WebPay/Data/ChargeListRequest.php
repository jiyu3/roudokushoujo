<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class ChargeListRequest extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof ChargeListRequest)) {
            return $params;
        }
        if (is_array($params)) {
            return new ChargeListRequest($params);
        }
        throw new InvalidRequestException('ChargeListRequest does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array('count', 'offset', 'created', 'customer', 'recursion', 'shop', 'captured', 'paid');
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
        $this->copyIfExists($this->attributes, $result, 'customer', 'queryParams');
        $this->copyIfExists($this->attributes, $result, 'recursion', 'queryParams');
        $this->copyIfExists($this->attributes, $result, 'shop', 'queryParams');
        $this->copyIfExists($this->attributes, $result, 'captured', 'queryParams');
        $this->copyIfExists($this->attributes, $result, 'paid', 'queryParams');

        return $result;
    }
}
