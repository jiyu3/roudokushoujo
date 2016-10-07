<?php

namespace WebPay\Data;

use WebPay\InvalidRequestException;
use WebPay\AbstractData;

class TokenRequestCreate extends AbstractData
{

    public static function create($params)
    {
        if ((is_object($params) && $params instanceof TokenRequestCreate)) {
            return $params;
        }
        if (is_array($params)) {
            return new TokenRequestCreate($params);
        }
        if ((is_object($params) && $params instanceof CardRequest)) {
            return new TokenRequestCreate(array('card' => $params));
        }
        throw new InvalidRequestException('TokenRequestCreate does not accept the given value', $params);
    }

    public function __construct(array $params)
    {
        $this->fields = array('card', 'customer', 'uuid');
        $params = $this->normalize($this->fields, $params);
        $params['card'] = is_array($params['card']) ? new CardRequest($params['card']) : $params['card'];
        $this->attributes = $params;
    }

    public function __set($key, $value)
    {
        $underscore = $this->decamelize($key);
        if ($underscore === 'card') { $value = is_array($value) ? new CardRequest($value) : $value; }
        $this->attributes[$underscore] = $value;
    }

    public function requestBody()
    {
        $result = array();
        $this->copyIfExists($this->attributes, $result, 'card', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'customer', 'requestBody');
        $this->copyIfExists($this->attributes, $result, 'uuid', 'requestBody');

        return $result;
    }

    public function queryParams()
    {
        $result = array();

        return $result;
    }
}
