<?php

namespace WebPay\Model;

abstract class AbstractModel
{
    /** @var array */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        $underscore = $this->decamelize($key);
        if (array_key_exists($underscore, $this->data)) {
            return $this->data[$underscore];
        }
        throw new \Exception('Undefined field ' . $key);

    }

    public function __set($key, $value)
    {
        throw new \Exception($key . ' is not able to override');
    }

    public function __isset($key)
    {
        if (array_key_exists($key, $this->data)) {
            return true;
        }

        if (array_key_exists($this->decamelize($key), $this->data)) {
            return true;
        }

        return false;
    }

    private function decamelize($str)
    {
        $proc = function ($r1) {
            return '_'.strtolower($r1[0]);
        };

        return preg_replace_callback('/([A-Z])/', $proc ,$str);
    }

    // WebPay -> array -> Entity
    protected function dataToObjectConverter(\WebPay\WebPay $client)
    {
        return function(array $item) use ($client) {
            switch ($item['object']) {
            case 'charge':
                return new Charge($client, $item);
                break;
            case 'customer':
                return new Customer($client, $item);
                break;
            case 'event':
                return new Event($client, $item);
                break;
            case 'token':
                return new Token($client, $item);
                break;
            case 'account':
                return new Account($client, $item);
                break;
            default:
                throw new APIConnectionException('Unknown object type ' . $item['object'], null, null, null);
            }
        };
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $rawData = $this->__toArray();
        if (defined('JSON_PRETTY_PRINT'))
            $json = json_encode($rawData, JSON_PRETTY_PRINT);
        else
            $json = json_encode($rawData);

        return get_class($this) . ' ' . $json;
    }

    /**
     * Recursively convert internal classes to array
     * @return array
     */
    public function __toArray()
    {
        $result = array();
        foreach ($this->data as $k => $v) {
            if (is_object($v))
                $result[$k] = $v->__toArray();
            else if (is_array($v))
                $result[$k] = array_map(function ($vv) {
                        if (is_object($vv))
                            return $vv->__toArray();
                        else
                            return $vv;
                    },  $v);
            else
                $result[$k] = $v;
        }

        return $result;
    }
}
