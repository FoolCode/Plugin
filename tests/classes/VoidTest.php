<?php

class VoidTest extends PHPUnit_Framework_TestCase
{
	public function testForge()
	{
		$this->assertInstanceOf('Foolz\Plugin\Void', \Foolz\Plugin\Void::forge());
	}
}