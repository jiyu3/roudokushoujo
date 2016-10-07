<?php

namespace WebPay;

class AbstractData
{
    /** @var array */
    protected $attributes;

    /** @var array */
    protected $fields;

    public function __get($key='')
    {
    	if (!$key) {
    		return $this->attributes;
    	}
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        $underscore = $this->decamelize($key);
        if (array_key_exists($underscore, $this->attributes)) {
            return $this->attributes[$underscore];
        }
        throw new \Exception('Undefined field ' . $key);
    }

    public function __toString()
    {
        $result = '<' . get_class($this) . "\n";
        foreach ($this->attributes as $k => $v) {
            $result .= '  ' . $this->camelize($k) . ': ' . $this->stringifyField($v) . "\n";
        }

        return $result . '>';
    }

    private function stringifyField($value)
    {
        if ($this->isAssoc($value)) {
            $result = "[\n";
            foreach ($value as $k => $v) {
                $result .= '    ' . $k . ': ' . $this->stringifyField($v) . "\n";
            }

            return $result . "  ]";
        } elseif (is_array($value)) {
            $data = array();
            foreach ($value as $elem) {
                array_push($data, $this->stringifyField($elem));
            }

            return '[' . implode(", ", $data) . ']';
        } elseif ($value === null) {
            return "null";
        } elseif ($value === true) {
            return "true";
        } elseif ($value === false) {
            return "false";
        } else {
            return implode("\n  ", explode("\n", (string) $value));
        }
    }

    protected function isAssoc($value)
    {
        return is_array($value) && array_diff_key($value,array_keys(array_keys($value)));
    }

    protected function camelize($str)
    {
        $value = preg_replace_callback("/([_]?([a-z0-9]+))/", function ($matches) { return ucwords($matches[2]);}, $str);

        return strtolower($value[0]) . substr($value, 1);
    }

    protected function decamelize($str)
    {
        $proc = function ($r1) {
            return '_'.strtolower($r1[0]);
        };

        return preg_replace_callback('/([A-Z])/', $proc ,$str);
    }

    protected function normalize($fields, $params)
    {
        $result = array();
        foreach ($fields as $f) {
            $result[$f] = array_key_exists($f, $params) ? $params[$f] : null;
        }

        return $result;
    }

    protected function copyIfExists($from ,&$to, $key, $recFun)
    {
        $v = $from[$key];
        if ($v === null) {
            return;
        }
        if (is_object($v) && method_exists($v, $recFun)) {
            $to[$key] = $v->$recFun();
        } else {
            $to[$key] = $v;
        }
    }
}
