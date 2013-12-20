<?php
namespace x3\Functional;

class Functional
{
    /**
     * Return the method with reversed argument order
     *
     * @param callback $method The method to reverse
     *
     * @return closure The reversed method
     */
    public static function reverseArgs($method)
    {
        return function() use ($method) {
            return call_user_func_array($method, array_reverse(func_get_args()));
        };
    }
    /**
     * Curry `$callback` with the passed arguments
     *
     * @param callable $callback The callback to curry
     * @param mixed $arg ... The arguments to add by default
     *
     * @return closure The curried function
     */
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

        return function() use ($callable, &$store) {
            $args = func_get_args();
            $key = json_encode($args);

            if (!isset($store[$key])) {
                $store[$key] = call_user_func_array($callable, $args);
            }

            return $store[$key];
        };
    }
}
