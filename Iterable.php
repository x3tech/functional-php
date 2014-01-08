<?php
namespace x3\Functional;

use \Traversable;

use x3\Functional\Functional as F;
use x3\Functional\Dict as D;
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
     * array_map but for iterables instead of just arrays
     *
     * @param callable $callback The mapping callback
     * @param Iterable $iter     The iterable
     *
     * @return array Mapped results
     */
    public static function map($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('$callback isn\'t callable');
        }

        $iter = D::zip(array_slice(func_get_args(), 1));

        return static::reduce($iter, function ($result, $item) use ($callback) {
            $result[] = call_user_func_array($callback, $item);
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
     * Todo:
     *   Also pass array/iterable keys (No uniform way to do this?)
     *
     * @param callable $callback  The callback to use
     * @param Iterable $iter, ... Data to map
     * @param bool     $multiDict Whether to allow multiple values per key
     *
     * @return array The mapped result
     */
    public static function mapKeys($callback)
    {
        $restArgs = array_slice(func_get_args(), 1);
        if (is_bool(end($restArgs))) {
            list($iter, $multiDict) = [
                array_slice($restArgs, 0, -1),
                end($restArgs)
            ];
        } else {
            list($iter, $multiDict) = [
                $restArgs,
                false
            ];
        }

        return D::pairsToDict(
            call_user_func_array(
                'x3\Functional\Iterable::map',
                array_merge([$callback], static::toArray($iter))
            ),
            $multiDict
        );
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

    /**
     * Convert an iterable to an array
     *
     * @param Iterable $iter Iterable to convert
     *
     * @return array Result
     */
    public static function toArray($iter)
    {
        if (!is_array($iter) && !($iter instanceof Traversable)) {
            throw new \InvalidArgumentException('$iter is not Iterable');
        }

        if (is_array($iter)) {
            return $iter;
        }
        return static::reduce($iter, function ($result, $item, $key) {
            $result[$key] = $item;
            return $result;
        }, []);
    }
}
