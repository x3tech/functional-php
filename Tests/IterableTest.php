<?php
namespace x3\Functional\Tests;

use \ArrayObject;

use x3\Functional\Iterable as I;
use x3\Functional\Map;

class IterableTest extends \PHPUnit_Framework_TestCase
{
    public function testMultiPad()
    {
        $arrayA = [0, 1, 2, 3];
        $arrayB = [1, 2, 3];

        $this->assertEquals(
            [[0, 1, 2, 3], [1, 2, 3, 'foo']],
            I::multiPad([$arrayA, $arrayB], 'foo')
        );
    }

    public function testMap()
    {
        $testArray = [0, 1, 2, 3];
        $callback = function ($input) {
            return $input * 2;
        };

        $this->assertEquals([0, 2, 4, 6], I::map($callback, $testArray));
    }

    public function testMapMultiple()
    {
        $arrayA = [0, 1, 2, 3];
        $arrayB = [1, 2, 3, 4];
        $callback = function ($valA, $valB) {
            return $valA + $valB;
        };

        $this->assertEquals([1, 3, 5, 7], I::map($callback, $arrayA, $arrayB));
    }

    public function testMapMultipleUnequal()
    {
        $arrayA = [0, 1, 2, 3];
        $arrayB = [1, 2, 3];
        $callback = function ($valA, $valB) {
            return [$valA, $valB];
        };

        $this->assertEquals(
            [[0, 1], [1, 2], [2, 3], [3, null]],
            I::map($callback, $arrayA, $arrayB)
        );
    }

    public function testWalk()
    {
        $calledValue = false;
        $callback = function ($value) use (&$calledValue) {
            $calledValue = $value;
        };

        I::walk(['test'], $callback);
        $this->assertEquals('test', $calledValue);
    }

    public function testMapKeys()
    {
        $testArray = [0, 1, 2, 3];
        $callback = function ($input) {
            return [$input + 1, $input * 2];
        };

        $this->assertEquals(
            [1 => 0, 2 => 2, 3 => 4, 4 => 6],
            I::mapKeys($callback, $testArray)
        );
    }

    public function testGroupBy()
    {
        $testArray = [
            ['type' => 'foo', 'value' => 1],
            ['type' => 'foo', 'value' => 2],
            ['type' => 'bar', 'value' => 1]
        ];
        $resultArray = [
            'foo' => [
                ['type' => 'foo', 'value' => 1],
                ['type' => 'foo', 'value' => 2]
            ],
            'bar' => [
                ['type' => 'bar', 'value' => 1]
            ]
        ];

        $this->assertEquals(
            $resultArray,
            I::groupBy($testArray, Map::key('type'))
        );
    }

    public function testFindIf()
    {
        $testArray = [1, 2, 3];
        $callback = function ($input) {
            return $input > 1;
        };

        $this->assertEquals(2, I::findIf($testArray, $callback));
    }

    public function testReduce()
    {
        $testArray = [1, 2, 3];
        $callback = function ($result, $value) {
            return $result + $value;
        };
        $this->assertEquals(6, I::reduce($testArray, $callback, 0));
    }

    public function testPluck()
    {
        $testArray = [['col' => 'value'], ['col' => 'value2']];
        $this->assertEquals(['value', 'value2'], I::pluck($testArray, 'col'));
    }

    public function testGroupValues()
    {
        $groupValues = I::groupValues('col', 'col2');
        $testArray = [
            ['col' => 'value', 'col2' => 'value3'],
            ['col' => 'value2', 'col2' => 'value4']
        ];
        $this->assertEquals(
            ['col' => ['value', 'value2'], 'col2' => ['value3', 'value4']],
            $groupValues($testArray)
        );
    }

    public function testToArrayKeys()
    {
        $this->assertEquals([3 => 1, 1 => 3], I::toArray(new ArrayObject([3 => 1, 1 => 3])));
    }

    public function testToArray()
    {
        $this->assertEquals([1, 2], I::toArray(new ArrayObject([1, 2])));
    }

    public function testToArrayInvalidArgument()
    {
        $this->setExpectedException('InvalidArgumentException');
        I::toArray(null);
    }
}
