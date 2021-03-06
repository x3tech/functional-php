<?php
namespace x3\Functional;

class Debug
{
    /**
     * Return and dump contents of $var, useful for debugging function chains
     *
     * @param mixed $var
     *
     * @return mixed Just $var
     */
    public static function tap($var)
    {
        var_dump($var);
        return $var;
    }

    /**
     * Returns a callback to Debug::tab
     *
     * @return callable
     */
    public static function tapCb()
    {
        return function ($var) {
            return self::tap($var);
        };
    }
}
