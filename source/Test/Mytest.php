<?php
/**
 *
 * @author:  jhyang
 * @version: 1.1.0
 * @change:
 *    1. 16/12/5 jhyang: 创建；
 */
namespace Test;

class Mytest extends \PHPUnit_Framework_TestCase
{
    public function testMyFirstCase()
    {
        $stack = [];
        $this->assertEquals(1, count($stack));
        array_push($stack, 'foo');
        $this->assertEquals('foo', $stack[count($stack)-1]);
        $this->assertEquals(1, count($stack));
        $this->assertEquals('foo', array_pop($stack));
        $this->assertEquals(0, count($stack));
    }
}