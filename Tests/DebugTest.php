<?php
namespace x3\Functional\Tests;

use x3\Functional\Debug as D;

class DebugTest extends \PHPUnit_Framework_TestCase
{
    public function testTap()
    {
        ob_start();
        $var = D::tap('foo');
        $output = ob_get_clean();
        $this->assertEquals('foo', $var, 'Return value failed');
        $this->assertEquals("string(3) \"foo\"\n", $output, 'Dump failed');
    }
}
