<?php
namespace x3\Functional;

/**
 * Placeholder class, used for placeholders when currying
 */
class ArgPlaceholder
{
    /**
     * __toString is required for array_search to know what to do with this
     */
    public function __toString()
    {
        return '__x3\Functional\ArgPlaceHolder__';
    }
}
