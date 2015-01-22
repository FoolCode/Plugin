<?php

use Foolz\Plugin\Plugin;
use Foolz\Plugin\Loader;

class PluginTest extends PHPUnit_Framework_TestCase
{
    public function unlinkConfig()
    {
        if (file_exists(__DIR__.'/../../tests/mock/foolz/fake/composer.php')) {
            unlink(__DIR__.'/../../tests/mock/foolz/fake/composer.php');
        }
    }

    public function testConstruct()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $this->assertInstanceOf('Foolz\Plugin\Plugin', $plugin);
    }

    /**
     * @expectedException \DomainException
     */
    public function testConstructThrows()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/blabla');
    }

    public function testGetSetLoader()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $loader = new Loader();
        $plugin->setLoader($loader);
        $this->assertSame($loader, $plugin->getLoader());
    }

    public function testGetDir()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake');
        $this->assertFalse(__DIR__.'/../../tests/mock/foolz/fake' === $plugin->getDir());

        // it always adds a trailing slash
        $this->assertSame(__DIR__.'/../../tests/mock/foolz/fake/', $plugin->getDir());
    }

    public function testGetJsonConfig()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $this->assertArrayHasKey('name', $plugin->getJsonConfig());
    }

    public function testGetJsonConfigKey()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $this->assertSame('Fake', $plugin->getJsonConfig('extra.name'));
    }

    public function testGetJsonConfigKeyFallback()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $this->assertSame('Fake', $plugin->getJsonConfig('extra.doesntexist', 'Fake'));
    }

    /**
     * @expectedException \DomainException
     */
    public function testGetJsonConfigKeyThrows()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $this->assertSame('Fake', $plugin->getJsonConfig('extra.doesntexist'));
    }

    /**
     * @expectedException \DomainException
     */
    public function testGetJsonConfigBrokenJsonThrows()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/broken_json/');
        $plugin->getJsonConfig();
    }

    public function testJsonToConfig()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $plugin->jsonToConfig();
        $this->assertSame($plugin->getJsonConfig(), $plugin->getConfig());
        $this->unlinkConfig();
    }

    public function testGetConfig()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $this->assertArrayHasKey('name', $plugin->getConfig());
        $this->unlinkConfig();
    }

    public function testGetConfigKey()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $this->assertSame('Fake', $plugin->getConfig('extra.name'));
        $this->unlinkConfig();
    }

    public function testGetConfigKeyFallback()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $this->assertSame('Fake', $plugin->getConfig('extra.doesntexist', 'Fake'));
        $this->unlinkConfig();
    }

    /**
     * @expectedException \DomainException
     */
    public function testGetConfigKeyFallbackThrows()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $plugin->getConfig('extra.doesntexist');
        $this->unlinkConfig();
    }

    public function testRefreshConfig()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $plugin->getConfig();

        $plugin->refreshConfig();
        $this->assertFalse(file_exists(__DIR__.'/../../tests/mock/foolz/fake/composer.php'));
        $this->unlinkConfig();
    }

    public function testBootstrap()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $plugin->bootstrap();
        // we set a trap in the bootstrap file
        $result = \Foolz\Plugin\Hook::forge('the.bootstrap.was.loaded')->execute()->get('no load');
        $this->assertSame('success', $result);
        $this->unlinkConfig();
    }

    public function testExecute()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $plugin->execute();
        $result = \Foolz\Plugin\Hook::forge('foolz\plugin\plugin.execute.foolz/fake')
            ->execute()->get('no load');
        $this->assertSame('success', $result);
        $this->unlinkConfig();
    }

    public function testInstall()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $plugin->install();
        $result = \Foolz\Plugin\Hook::forge('foolz\plugin\plugin.install.foolz/fake')
            ->execute()->get('no load');
        $this->assertSame('success', $result);
        $this->unlinkConfig();
    }

    public function testUninstall()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $plugin->uninstall();
        $result = \Foolz\Plugin\Hook::forge('foolz\plugin\plugin.uninstall.foolz/fake')
            ->execute()->get('no load');
        $this->assertSame('success', $result);
        $this->unlinkConfig();
    }

    public function testUpgrade()
    {
        $plugin = new Plugin(__DIR__.'/../../tests/mock/foolz/fake/');
        $plugin->upgrade();
        $result = \Foolz\Plugin\Hook::forge('foolz\plugin\plugin.upgrade.foolz/fake')
            ->setObject($this)
            ->setParam('old_revision', $plugin->getConfig('extra.revision', 0))
            ->setParam('new_revision', $plugin->getJsonConfig('extra.revision', 0))
            ->execute();
        $this->assertSame('success', $result->get('no load'));
        $this->assertSame(0, $result->getParam('old_revision'));
        $this->assertSame(0, $result->getParam('new_revision'));
        $this->unlinkConfig();
    }
}
