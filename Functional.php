<?php
namespace x3\Functional;

class Functional
{
    public static function mapMethod($method)
    {
        $args = array_slice(func_get_args(), 2);
        return function($object) use($method, $args) {
            return $object->$method($args);
        };
    }

    public static function mapAttribute($attribute)
    {
        return function($object) use($attribute) {
            return $object->$attribute;
        };
    }
}
