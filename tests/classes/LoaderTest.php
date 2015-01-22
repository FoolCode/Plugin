<?php

use Foolz\Plugin\Loader;

class LoaderTest extends PHPUnit_Framework_TestCase
{
    public function testGetPlugin()
    {
        $new = Loader::forge('default');
        $new->addDir('test', __DIR__.'/../../tests/mock/');
        $plugin = $new->get('test', 'foolz/fake');
        $this->assertInstanceOf('Foolz\Plugin\Plugin', $plugin);
    }
}
