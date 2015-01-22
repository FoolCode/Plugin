<?php

use Foolz\Plugin\Result as Result;

class ResultTest extends PHPUnit_Framework_TestCase
{
    public function testResult()
    {
        $std = new stdClass();
        $new = new Result(array('param1' => 'test'), $std);

        $this->assertSame($std, $new->getObject());
    }

    public function testSetGet()
    {
        $new = new Result();
        $new->set('bla');
        $this->assertSame('bla', $new->get());
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testSetGetThrowsOutOfBounds()
    {
        $new = new Result();
        $new->getObject();
    }

    public function testSetGetFallback()
    {
        $new = new Result();
        $this->assertSame('blabla', $new->get('blabla'));
    }

    public function testSetGetParam()
    {
        $arr = array('param1' => 'test', 'param2' => 'testtest');
        $new = new Result($arr);

        $this->assertSame($arr, $new->getParams());

        $this->assertSame('test', $new->getParam('param1'));

        $new->setParam('param1', 'test1');

        $this->assertSame($arr, $new->getParams(true));
        $this->assertSame('test', $new->getParam('param1', true));
        $this->assertSame('test1', $new->getParam('param1'));
    }

    public function testSetGetParams()
    {
        $arr = array('param1' => 'test', 'param2' => 'testtest');
        $new = new Result();
        $new->setParams($arr);

        $this->assertSame($arr, $new->getParams());
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetParamThrowsOutOfBounds()
    {
        $new = new Result();
        $new->getParam('herp');
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetParamOrigThrowsOutOfBounds()
    {
        $new = new Result();
        $new->getParam('herp', true);
    }
}
