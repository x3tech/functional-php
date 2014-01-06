<?php
namespace x3\Functional;

class Iterable
{
    /**
     * array_map but for iterables instead of just arrays
     *
     * @param Iterable $iter     The iterable
     * @param callable $callback The mapping callback
     *
     * @return array Mapped results
     */
    public static function map($iter, $callback)
    {
        return static::reduce($iter, function($result, $item, $key) use ($callback) {
            $result[] = call_user_func($callback, $item, $key);
            return $result;
        }, []);
    }

    /**
     * array_walk but for iterables
     *
     * @param Iterable $iter     The iterable
     * @param callable $callback The callback to execute for each item
     *
     * @return null
     */
    public static function walk($iter, $callback)
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
    public static function mapKeys($iter, $callback, $multiDict = false)
    {
        return static::pairsToDict(static::map($iter, $callback), $multiDict);
    }

    /**
     * Group items of `$iter` by the result of `$keycallback`
     *
     * @param Iterable $iter        The iterable to parse
     * @param callback $keyCallback A callback that returns the key to use
     *
     * @return array The grouped items
     */
    public static function groupBy($iter, $keyCallback)
    {
        $callback = function($item) use ($keyCallback) {
            return [$keyCallback($item), $item];
        };

        return static::mapKeys($iter, $callback, true);
    }

    /**
     * Find the first item that callback returns true for
     *
     * @param Iterable $iter     The iterable
     * @param callable $callback The callback for checking
     *
     * @return bool|mixed The found item or false if none matched
     */
    public static function findIf($iter, $callback)
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
    public static function reduce($iter, $callback, $initial = null)
    {
        $result = $initial;
        foreach ($iter as $key => $item) {
            $result = $callback($result, $item, $key);
        }

        return $result;
    }

    /**
     * Convert key-value pairs to a key=>value array (dict)
     *
     * @param iterable $pairs The pairs to convert
     * @param bool     $multiDict Wether to allow multiple values per key
     *
     * @return array key=>value dict
     */
    public static function pairsToDict($pairs, $multiDict = false)
    {
        $callback = function($result, $pair) use ($multiDict) {
            list($key, $value) = $pair;
            if($multiDict) {
                if(!isset($result[$key])) {
                    $result[$key] = [];
                }
                $result[$key][] = $value;
            } else {
                $result[$key] = $value;
            }

            return $result;
        };

        return static::reduce($pairs, $callback, []);
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
        return static::map($array, Map::key($column));
    }

    /**
     * Returns a closure that groups values by array key
     *
     * @param mixed $key, ... Keys to pluck and group by
     *
     * return callable Closure that accepts an iterable to group values from
     */
    public static function groupValues()
    {
        $keys = func_get_args();

        return function($subjects) use ($keys) {
            $callback = function($key) use ($subjects) {
                return [$key, static::pluck($subjects, $key)];
            };

            return static::mapKeys($keys, $callback);
        };
    }
}
