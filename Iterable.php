<?php
namespace x3\Functional;

use x3\Functional\Functional as F;
use x3\Functional\ArgPlaceholder as _;

class Iterable
{
    /**
     * Pads arrays to the length of the longest array with the value of $pad
     *
     * @param Iterable $arrays To pad
     * @param mixed    $pad    What to pad with
     *
     * @return array Padded arrays
     */
    public static function multiPad($arrays, $pad = null)
    {
        $longest = max(static::map('count', $arrays));
        return static::map(F::curry('array_pad', new _, $longest, $pad), $arrays);
    }

    /**
     * Takes an iterable of arrays and zips them (Similar to Python's `zip`)
     *
     * Example:
     *   >>> Iterable::zip([[1, 2, 3], [4, 5]])
     *   [[1, 4], [2, 5], [3, null]]
     *
     * Note:
     *   as opposed to Python arrays are padded with null if they're shorter
     *   than the longest array.
     *
     * @param Iterable $arrays The arrays to zip
     *
     * @return array Zipped arrays
     */
    public static function zip(array $arrays)
    {
        return static::internalZip($arrays, 'x3\Functional\Iterable::map');
    }

    /**
     * @internal
     *
     * Internal use, as `map` also uses zip, but that would cause infinite
     * recursion we use an internal function with a specifiable map function
     */
    protected static function internalZip(array $arrays, $mapFunc = 'array_map')
    {
        $callback = function () {
            return func_get_args();
        };

        return call_user_func_array(
            $mapFunc,
            array_merge([$callback], $arrays)
        );
    }

    /**
     * array_map but for iterables instead of just arrays
     *
     * @param callable $callback The mapping callback
     * @param Iterable $iter     The iterable
     *
     * @return array Mapped results
     */
    public static function map($callback, $iter)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('$callback isn\'t callable');
        }

        return static::reduce($iter, function ($result, $item, $key) use ($callback) {
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
        foreach ($iter as $item) {
            $callback($item);
        }
    }

    /**
     * Similar to imap but also map the keys
     *
     * @param callable $callback  The callback to use
     * @param array    $iter      Data to map
     * @param bool     $multiDict Whether to allow multiple values per key
     *
     * @return array The mapped result
     */
    public static function mapKeys($callback, $iter, $multiDict = false)
    {
        return static::pairsToDict(static::map($callback, $iter), $multiDict);
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
        $callback = function ($item) use ($keyCallback) {
            return [$keyCallback($item), $item];
        };

        return static::mapKeys($callback, $iter, true);
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
        $callback = function ($result, $pair) use ($multiDict) {
            list($key, $value) = $pair;
            if ($multiDict) {
                if (!isset($result[$key])) {
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
        return static::map(Map::key($column), $array);
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

        return function ($subjects) use ($keys) {
            $callback = function ($key) use ($subjects) {
                return [$key, static::pluck($subjects, $key)];
            };

            return static::mapKeys($callback, $keys);
        };
    }
}
