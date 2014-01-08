<?php
namespace x3\Functional\Tests;

use \ArrayObject;

use x3\Functional\Dict as D;

class DictTest extends \PHPUnit_Framework_TestCase
{

    public function testPairsToDict()
    {
        $testPairs = [['a', 1], ['a', 3], ['b', 2]];
        $this->assertEquals(
            ['a' => 3, 'b' => 2],
            D::pairsToDict($testPairs),
            'Failed with singledict'
        );
        $this->assertEquals(
            ['a' => [1, 3], 'b' => [2]],
            D::pairsToDict($testPairs, true),
            'Failed with multidict'
        );

        $this->assertEquals(
            ['a' => 3, 'b' => 2],
            D::pairsToDict(new ArrayObject($testPairs)),
            'Iterable failed'
        );
    }

    public function testDictToPairs()
    {
        $testDict = ['a' => 1, 'b' => 2];
        $this->assertEquals([['a', 1], ['b', 2]], D::dictToPairs($testDict));
        $this->assertEquals(
            [['a', 1], ['b', 2]],
            D::dictToPairs(new ArrayObject($testDict)),
            'Iterable failed'
        );
    }

    public function testZip()
    {
        $arrayA = [0, 1, 2, 3];
        $arrayB = [1, 2, 3];

        $this->assertEquals(
            [[0, 1], [1, 2], [2, 3], [3, null]],
            D::zip([$arrayA, $arrayB])
        );
    }
}
