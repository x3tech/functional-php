<?php
namespace x3\Functional\Tests;

use x3\Functional\Functional as F;
use x3\Functional\ArgPlaceholder as _;

class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    public function testReverseArgs()
    {
        $reversed = F::reverseArgs(function () {
            return func_get_args();
        });

        $this->assertEquals([2, 1], $reversed(1, 2), 'Arguments not reversed');
    }

    public function testCurry()
    {
        $curried = F::curry(function () {
            return func_get_args();
        }, 1);

        $this->assertEquals([1, 2], $curried(2), 'Method not curried');
    }

    public function testPlaceholderCurry()
    {
        $curried = F::curry(function () {
            return func_get_args();
        }, new _, 1);

        $this->assertEquals([2, 1], $curried(2), 'Method not curried');
    }

    /**
     * Test the compose method, and verify that it runs in the correct order
     */
    public function testCompose()
    {
        $composed = F::compose(function ($a) {
            return $a*2;
        }, function ($a) {
            return $a+1;
        });

        $this->assertEquals(4, $composed(1));
    }

    /**
     * TODO Various datatypes
     */
    public function testMemoize()
    {
        $memoized = F::memoize(function ($min, $max) {
            return rand($min, $max);
        });

        $this->assertEquals($memoized(1, 100), $memoized(1, 100));
        $this->assertEquals($memoized(50, 70), $memoized(50, 70));
    }
}
