<?php

use Foolz\Plugin\Loader;

class LoaderTest extends PHPUnit_Framework_TestCase
{
	public function testConstruct()
	{
		$new = new Loader();
	}

	public function testForge()
	{
		$new = Loader::forge('default');
		$new2 = Loader::forge('default2');

		$this->assertInstanceOf('\Foolz\Plugin\Loader', $new);
		$this->assertFalse($new === $new2);
	}

	public function testDestroy()
	{
		$new = Loader::forge('default');
		Loader::destroy('default');
		$new2 = Loader::forge('default');
		$this->assertFalse($new === $new2);
	}

	public function testClassLoaderEmpty()
	{
		$new = Loader::forge('default');
		// didn't add the class
		$new->classLoader('Foolz\Fake\Fake');
		$this->assertFalse(class_exists('Foolz\Fake\Fake'));
	}

	public function testClassLoader()
	{
		$new = Loader::forge('default');
		$new->addClass('Foolz\Fake\Fake', __DIR__.'/../../tests/mock/foolz/fake/classes/Foolz/Fake/Fake.php');
		$new->classLoader('Foolz\Fake\Fake');
		$this->assertTrue(class_exists('Foolz\Fake\Fake'));
	}

	/**
	 * @expectedException \DomainException
	 */
	public function testAddDirThrows()
	{
		$new = Loader::forge('default');
		$new->addDir('test', __DIR__.'/../../tests/moewufck/');
	}

	public function testAddDirWithoutName()
	{
		$new = Loader::forge('default');
		$dir = __DIR__.'/../../tests/mock/';
		$new->addDir($dir);
		$array = $new->getAll();
		$this->assertArrayHasKey($dir, $array);
		$this->assertArrayHasKey('foolz/fake', $array[$dir]);
	}

	public function testAddDir()
	{
		$new = Loader::forge('default');
		$new->addDir('test', __DIR__.'/../../tests/mock/');
		$array = $new->getAll();
		$this->assertArrayHasKey('test', $array);
		$this->assertArrayHasKey('foolz/fake', $array['test']);
	}

	public function testRemoveDir()
	{
		$new = Loader::forge('default');
		$new->addDir('test', __DIR__.'/../../tests/mock/');
		$new->getAll();
		$new->removeDir('test');
		$array = $new->getAll();
		$this->assertArrayNotHasKey('test', $array);
	}

	/**
	 * @expectedException \OutOfBoundsException
	 */
	public function testGetPluginsThrow()
	{
		$new = Loader::forge('default');
		$new->addDir('test', __DIR__.'/../../tests/mock/');
		$new->getAll('trest');
	}

	public function testGetPluginsKey()
	{
		$new = Loader::forge('default');
		$new->addDir('test', __DIR__.'/../../tests/mock/');
		$array = $new->getAll('test');
		$this->assertArrayHasKey('foolz/fake', $array);
	}

	public function testGetPlugin()
	{
		$new = Loader::forge('default');
		$new->addDir('test', __DIR__.'/../../tests/mock/');
		$plugin = $new->get('test', 'foolz/fake');
		$this->assertInstanceOf('Foolz\Plugin\Plugin', $plugin);
	}

	/**
	 * @expectedException \OutOfBoundsException
	 */
	public function testGetPluginThrows()
	{
		$new = Loader::forge('default');
		$new->addDir('test', __DIR__.'/../../tests/mock/');
		$plugin = $new->get('test', 'foolz/faker');
	}
}