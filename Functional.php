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
     * Return a closure that calls `$method` on the object passed to it
     * Mostly used for common *map operations
     *
     * @param string $method The method to call
     *
     * @return closure Calls `$method` on passed object
     */
    public static function mapMethod($method)
    {
        $args = array_slice(func_get_args(), 1);
        return function($object) use($method, $args) {
            return call_user_func_array(array($object, $method), $args);
        };
    }

    /**
     * Return a closure that returns `$attribute` of the object passed to it
     * Mostly used for common *map operations
     *
     * @param string $attribute The attribute to reutrn
     *
     * @return closure Returns `$attribute` from passed object
     */
    public static function mapAttribute($attribute)
    {
        return function($object) use($attribute) {
            return $object->$attribute;
        };
    }

    /**
     * Returns a closure that groups values by array key
     *
     * @param mixed $key, ... Keys to pluck and group by
     *
     * return callable Closure that accepts an array to group values from
     */
    public static function groupValues()
    {
        $keys = func_get_args();

        return function($subjects) use ($keys) {
            $callback = function($key) use ($subjects) {
                return array($key, static::pluck($subjects, $key));
            };

            return static::arrayMapKeys($callback, $keys);
        };
    }

    /**
     * Similar to array_map but also map the keys
     *
     * @param callable $callback  The callback to use
     * @param array    $data      Data to map
     * @param bool     $multiDict Whether to allow multiple values per key
     *
     * @return array The mapped result
     */
    public static function arrayMapKeys($callback, $data, $multiDict = false)
    {
        return static::pairsToDict(array_map($callback, $data), $multiDict);
    }

    /**
     * Convert key-value pairs to a key=>value array (dict)
     *
     * @param array $pairs The pairs to convert
     * @param bool  $multiDict Wether to allow multiple values per key
     *
     * @return array key=>value dict
     */
    public static function pairsToDict(array $pairs, $multiDict = false)
    {
        $callback = function($result, $pair) use ($multiDict) {
            list($key, $value) = $pair;
            if($multiDict) {
                if(!isset($result[$key])) {
                    $result[$key] = array();
                }
                $result[$key][] = $value;
            } else {
                $result[$key] = $value;
            }

            return $result;
        };

        return array_reduce($pairs, $callback, array());
    }

    /**
     * Pluck `$column` from the children of `$array`
     *
     * @param array $array  The array to pluck from
     * @param mixed $column The column to return
     *
     * @return array The plucked columns
     */
    public static function pluck($array, $column)
    {
        return array_map(Functional::mapKey($column), $array);
    }

    /**
     * Return a closure that returns the value of `$key` for the passed array
     * Mostly used for common *map operations
     *
     * @param string $key The key
     *
     * @return closure Returns value of `$key` on passed array
     */
    public static function mapKey($key)
    {
        return function($array) use ($key) {
            return $array[$key];
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

    /**
     * array_map but for iterables instead of just arrays
     *
     * @param Iterable $iter     The iterable
     * @param callable $callback The mapping callback
     *
     * @return array Mapped results
     */
    public static function imap($iter, $callback)
    {
        return static::ireduce($iter, function($result, $item, $key) use ($callback) {
            $result[] = call_user_func($callback, $item, $key);
            return $result;
        }, array());
    }

    /**
     * array_walk but for iterables
     *
     * @param Iterable $iter     The iterable
     * @param callable $callback The callback to execute for each item
     *
     * @return null
     */
    public static function iwalk($iter, $callback)
    {
        foreach($iter as $item) {
            $callback($item);
        }
    }

    /**
     * Similar to imap but also map the keys
     *
     * @param callable $callback  The callback to use
     * @param array    $data      Data to map
     * @param bool     $multiDict Whether to allow multiple values per key
     *
     * @return array The mapped result
     */
    public static function imapKeys($iter, $callback, $multiDict = false)
    {
        return static::pairsToDict(static::imap($iter, $callback), $multiDict);
    }

    /**
     * Group items of `$iter` by the result of `$keycallback`
     *
     * @param Iterable $iter        The iterable to parse
     * @param callback $keyCallback A callback that returns the key to use
     *
     * @return array The grouped items
     */
    public static function igroupBy($iter, $keyCallback)
    {
        $callback = function($item) use ($keyCallback) {
            return array($keyCallback($item), $item);
        };

        return static::imapKeys($iter, $callback, true);
    }

    /**
     * Find the first item that callback returns true for
     *
     * @param Iterable $iter     The iterable
     * @param callable $callback The callback for checking
     *
     * @return bool|mixed The found item or false if none matched
     */
    public static function ifindIf($iter, $callback)
    {
        foreach ($iter as $item) {
            if ($callback($item)) {
                return $item;
            }
        }

        return false;
    }

    /**
     * array_reduce but for iterables
     *
     * @param Iterable $iter     The iterable
     * @param callable $callback The callback for the reduce
     * @param mixed    $initial  Initial value for the reduce operation
     *
     * @return mixed The result of the reduce operation
     */
    public static function ireduce($iter, $callback, $initial = null)
    {
        $result = $initial;
        foreach ($iter as $key => $item) {
            $result = $callback($result, $item, $key);
        }

        return $result;
    }

}
