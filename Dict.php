<?php
namespace x3\Functional;

use x3\Functional\Iterable as I;
use x3\Functional\Functional as F;

class Dict
{
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
    public static function zip($arrays)
    {
        $callback = function () {
            return func_get_args();
        };

        return call_user_func(F::compose(
            F::curry('call_user_func_array', 'array_map'),
            F::curry('array_merge', [$callback]),
            F::curry('array_map', 'x3\Functional\Iterable::toArray'),
            'x3\Functional\Iterable::toArray'
        ), $arrays);
    }

    /**
     * Convert a dict to pairs
     *
     * Example:
     *   >>> Iterable::dictToPairs(['a' => 1, 'b' => 2])
     *   [['a', 1], ['b', 2]]
     *
     * @param Iterable $iter The dict to convert
     *
     * @return array The pairs
     */
    public static function dictToPairs($iter)
    {
        $callback = function () {
            return func_get_args();
        };

        $dict = I::toArray($iter);
        return I::map($callback, array_keys($dict), $dict);
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

        return I::reduce($pairs, $callback, []);
    }

    /**
     * Get value from $dict with index $key and return $default if empty
     *
     * @param Dict  $dict    Dict to fetch from
     * @param mixed $key     Key to fetch
     * @param mixed $default Default value if empty
     *
     * @return mixed The value for $key or $default if empty
     */
    public static function get($dict, $key, $default = null)
    {
        return empty($dict[$key]) ? $default : $dict[$key];
    }
}
