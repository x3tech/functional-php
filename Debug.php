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
    public function tap($var)
    {
        var_dump($var);
        return $var;
    }
}
