<?php
namespace x3\Functional;

class Map
{
    /**
     * Return a closure that calls `$method` on the object passed to it
     * Mostly used for common *map operations
     *
     * @param string $method The method to call
     *
     * @return closure Calls `$method` on passed object
     */
    public static function method($method)
    {
        $args = array_slice(func_get_args(), 1);
        return function($object) use($method, $args) {
            return call_user_func_array([$object, $method], $args);
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
    public static function attribute($attribute)
    {
        return function($object) use($attribute) {
            return $object->$attribute;
        };
    }

    /**
     * Return a closure that returns the value of `$key` for the passed array
     * Mostly used for common *map operations
     *
     * @param string $key The key
     *
     * @return closure Returns value of `$key` on passed array
     */
    public static function key($key)
    {
        return function($array) use ($key) {
            return $array[$key];
        };
    }
}
