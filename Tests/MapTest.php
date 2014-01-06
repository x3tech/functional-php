<?php
namespace x3\Functional\Tests;

use x3\Functional\Map;

class MapTest extends \PHPUnit_Framework_TestCase
{
    public function testMapMethod()
    {
        $callback = Map::method('count');
        $testObject = new \ArrayObject([1]);

        $this->assertEquals(1, $callback($testObject));
    }

    public function testMapAttribute()
    {
        $callback = Map::attribute('var');
        $testObject = (object)['var' => 'test'];

        $this->assertEquals('test', $callback($testObject));
    }

    public function testMapKey()
    {
        $callback = Map::key('var');
        $testArray = ['var' => 'test'];

        $this->assertEquals('test', $callback($testArray));
    }
}
