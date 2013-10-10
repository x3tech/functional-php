<?php
namespace x3\Functional;

class Functional
{
    public static function reverseArgs($method)
    {
        return function() use ($method) {
            return $method(array_reverse(func_get_args()));
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
}
