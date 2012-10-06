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
}