<?php
namespace x3\Functional;

class Functional
{
    public static function reverseArgs($method)
    {
        return function() use ($method) {
            return call_user_func_array($method, array_reverse(func_get_args()));
        };
    }
    public static function mapMethod($method)
    {
        $args = array_slice(func_get_args(), 1);
        return function($object) use($method, $args) {
            return call_user_func_array(array($object, $method), $args);
        };
    }

    public static function mapAttribute($attribute)
    {
        return function($object) use($attribute) {
            return $object->$attribute;
        };
    }

    public static function groupValues()
    {
        $keys = func_get_args();

        return function($subjects) use ($keys) {
            $callback = function($key) use ($subjects) {
                return array($key, static::arrayColumn($subjects, $key));
            };

            return static::arrayMapKeys($callback, $keys);
        };
    }

    public static function arrayMapKeys($callback, $data, $multiDict = false)
    {
        return static::pairsToDict(array_map($callback, $data), $multiDict);
    }

    public static function pairsToDict(array $pairs, $multiDict = false)
    {
        $result = array();
        $callback = function($pair) use (&$result, $multiDict) {
            list($key, $value) = $pair;
            if($multiDict) {
                if(!isset($result[$key])) {
                    $result[$key] = array();
                }
                $result[$key][] = $value;
            } else {
                $result[$key] = $value;
            }
        };
        array_walk($pairs, $callback);


        return $result;
    }

    public static function arrayColumn($array, $column)
    {
        return array_map(Functional::mapKey($column), $array);
    }

    public static function mapKey($key)
    {
        return function($array) use ($key) {
            return $array[$key];
        };
    }

    public static function curry($callback)
    {
        $curryArgs = array_slice(func_get_args(), 1);

        return function() use ($callback, $curryArgs) {
            $args = array_merge($curryArgs, func_get_args());
            return call_user_func_array($callback, $args);
        };
    }

    public static function compose()
    {
        $callables = array_reverse(func_get_args());
        $callback = function($arg, $callable) {
            return call_user_func($callable, $arg);
        };

        return function($parameter) use ($callables, $callback) {
            return array_reduce($callables, $callback, $parameter);
        };
    }

    public static function memoize($callable)
    {
        $store = array();

        return function() use ($callable, $store) {
            $args = func_get_args();
            $key = json_encode($args);

            if (!isset($store[$key])) {
                $result = call_user_func_array($callable, $args);
                $store[$key] = $result;
            }

            return $result;
        };
    }

    public static function imap($iter, $callback)
    {
        $results = array();
        foreach($iter as $key => $item) {
            $results[] = call_user_func($callback, $item, $key);
        }

        return $results;
    }
    public static function iwalk($iter, $callback)
    {
        foreach($iter as $item) {
            $callback($item);
        }
    }

    public static function imapKeys($iter, $callback, $multiDict)
    {
        return static::pairsToDict(static::imap($iter, $callback), $multiDict);
    }

    public static function igroupBy($iter, $keyCallback)
    {
        $callback = function($item) use ($keyCallback) {
            return array($keyCallback($item), $item);
        };

        return static::imapKeys($iter, $callback, true);
    }

    public static function ifindIf($iter, $callback)
    {
        foreach ($iter as $item) {
            if ($callback($item)) {
                return $item;
            }
        }

        return false;
    }
}
