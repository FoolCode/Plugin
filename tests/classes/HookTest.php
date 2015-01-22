<?php

use Foolz\Plugin\Event as Event;
use Foolz\Plugin\Hook as Hook;

class HookTest extends PHPUnit_Framework_TestCase
{
    public function testHook()
    {
        $ev = new Event('testing');
        $ev->setCall(function($result) {
            $obj = $result->getObject();
            $obj->assertSame(1, 1);
            $result->set('success '.$result->getParam('test'));
        });

        $new = new Hook('testing');
        $result = $new->setObject($this)
            ->setParam('test', 'tester')
            ->execute();

        $this->assertSame('success tester', $result->get());

        // poor forge
        $result = Hook::forge('testing')
            ->setObject($this)
            ->setParams(array('test' => 'tester'))
            ->execute();

        $this->assertSame('success tester', $result->get());

        $ev = new Event('hardcore');
        $ev->setCall(function($result){
            $num = $result->getParam('num');
            $num++;
            $result->setParam('num', $num);
            $result->set($num);
        });

        $ev = new Event('hardcore');
        $ev->setCall(function($result){
            $num = $result->getParam('num');
            $num++;
            $result->setParam('num', $num);
            $result->set($num);
        });

        $result = Hook::forge('hardcore')
            ->setParam('num', 0)
            ->execute();

        $this->assertSame(2, $result->getParam('num'));
        $this->assertSame(2, $result->get());
    }

    public function testConstructArray()
    {
        $ev = new Event(array('easycore1', 'easycore2'));
        $ev->setCall(function($result){
            $num = $result->getParam('num');
            $num++;
            $result->setParam('num', $num);
            $result->set($num);
        });

        $result1 = Hook::forge('easycore1')
            ->setParam('num', 0)
            ->execute()
            ->get(0);

        $result2 = Hook::forge('easycore2')
            ->setParam('num', 0)
            ->execute()
            ->get(0);

        $this->assertSame(1, $result1);
        $this->assertSame(1, $result2);
    }

    public function testObjectInheritance()
    {
        $class = new \stdClass();
        $class->value = 'success';

        Event::forge('objecttests')
            ->setCall(function($result) {
                // over PHP 5.4 we can use $this
                if(version_compare(phpversion(), '5.4.0') >= 0)
                {
                    $result->set($this->value);
                }
                else
                {
                    $obj = $result->getObject();
                    $result->set($obj->value);
                }
            });

        $result = Hook::forge('objecttests')
            ->setObject($class)
            ->execute()
            ->get('failure');

        $this->assertSame('success', $result);
    }

    public function testDisable()
    {
        \Foolz\Plugin\Hook::disable('disable.me');

        \Foolz\Plugin\Event::forge('disable.me')
            ->setCall(function($result) {
                $result->setParam('result', 'unexpected');
            });

        $result = \Foolz\Plugin\Hook::forge('disable.me')
            ->setParam('result', 'expected')
            ->execute();

        $this->assertSame('expected', $result->getParam('result'));
    }

    public function testEnable()
    {
        \Foolz\Plugin\Hook::disable('disable.me2');
        \Foolz\Plugin\Hook::enable('disable.me2');

        \Foolz\Plugin\Event::forge('disable.me2')
            ->setCall(function($result) {
                $result->setParam('result', 'expected');
            });

        $result = \Foolz\Plugin\Hook::forge('disable.me2')
            ->setParam('result', 'unexpected')
            ->execute();

        $this->assertSame('expected', $result->getParam('result'));
    }
}
