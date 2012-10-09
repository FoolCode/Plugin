<?php

namespace Foolz\Plugin;

/**
 * Holds data on a plugin package
 *
 * @author Foolz <support@foolz.us>
 * @package Foolz\Plugin
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 */
class Plugin
{
	/**
	 * The Loader object that created this object
	 *
	 * @var \Foolz\Plugin\Loader
	 */
	protected $loader;

	/**
	 * The path to this plugin
	 *
	 * @var string
	 */
	protected $dir;

	/**
	 * Loaded JSON config
	 *
	 * @var null|array
	 */
	protected $json_config = null;

	/**
	 * Loaded PHP config
	 *
	 * @var null|array
	 */
	protected $config = null;

	/**
	 * Gets the instance of the loader
	 *
	 * @param type $loader
	 */
	public function __construct(\Foolz\Plugin\Loader $loader, $dir)
	{
		$this->loader = $loader;
		$this->dir = rtrim($dir,'/').'/';
	}

	/**
	 * Gets the loader that created this object
	 *
	 * @return \Foolz\Plugin\Loader
	 */
	public function getLoader()
	{
		return $this->loader;
	}

	/**
	 * Gets the path to the plugin
	 */
	public function getDir()
	{
		return $this->dir;
	}

	/**
	 * Adds a class for the autoloader
	 *
	 * @param string $class
	 * @param string $dir
	 * @return \Foolz\Plugin\Plugin
	 */
	public function addClass($class, $dir)
	{
		$this->getLoader()->addClass($class, $dir);
		return $this;
	}

	/**
	 * Gets the content of the config.json of the plugin
	 */
	public function getJsonConfig($section = null, $fallback = null)
	{
		if ($this->json_config === null)
		{
			$file = $this->getDir().'composer.json';

			if ( ! file_exists($file))
			{
				throw new \DomainException;
			}

			$this->json_config = json_decode(file_get_contents($file), true);

			if ($this->json_config === null)
			{
				throw new \DomainException;
			}
		}

		if ($section === null)
		{
			return $this->json_config;
		}

		// if there wasn't an actual fallback set
		if (func_num_args() !== 2)
		{
			return Util::dottedConfig($this->json_config, $section, new Void);
		}

		return Util::dottedConfig($this->json_config, $section, $fallback);
	}

	/**
	 * Converts the JSON to a PHP config to improve speed
	 *
	 * @return \Foolz\Plugin\Plugin
	 */
	public function jsonToConfig()
	{
		$config = $this->getJsonConfig();

		Util::saveArrayToFile($this->getDir().'config.php', $config);
		return $this;
	}

	/**
	 * Gets the content of the config
	 *
	 * @param type $section
	 * @param type $fallback
	 * @return type
	 * @throws \DomainException
	 */
	public function getConfig($section = null, $fallback = null)
	{
		if ($this->config === null)
		{
			$php_file = $this->getDir().'config.php';

			if (file_exists($php_file) === false)
			{
				$this->jsonToConfig();
			}

			$this->config = include $php_file;
		}

		if ($section === null)
		{
			return $this->config;
		}

		// if there wasn't an actual fallback set
		if (func_num_args() !== 2)
		{
			return Util::dottedConfig($this->config, $section, new Void);
		}

		return Util::dottedConfig($this->config, $section, $fallback);
	}

	/**
	 * Destroys the config.php to recreate it from the composer.json
	 */
	public function refreshConfig()
	{
		if (file_exists($this->getDir().'config.php'))
		{
			unlink($this->getDir().'config.php');
		}
	}

	public function clearJsonConfig()
	{
		$this->json_config = null;
	}

	public function clearConfig()
	{
		$this->config = null;
	}

	/**
	 * Runs the bootstrap file
	 *
	 * @return  \Foolz\Plugin\Plugin
	 */
	public function bootstrap()
	{
		include $this->getDir().'bootstrap.php';
		return $this;
	}

	/**
	 * Runs the execution block
	 *
	 * @return  \Foolz\Plugin\Plugin
	 */
	public function execute()
	{
		// clear the hook since we might have an old one
		\Foolz\Plugin\Event::clear('foolz\plugin\plugin.execute.'.$this->getJsonConfig('name'));

		$this->bootstrap();
		\Foolz\Plugin\Hook::forge('foolz\plugin\plugin.execute.'.$this->getJsonConfig('name'))
			->setObject($this)
			->execute();

		return $this;
	}

	/**
	 * Triggers the install methods for the plugin
	 *
	 * @return  \Foolz\Plugin\Plugin
	 */
	public function install()
	{
		// clear the hook since we might have an old one
		\Foolz\Plugin\Event::clear('foolz\plugin\plugin.install.'.$this->getJsonConfig('name'));

		// execute the bootstrap to get the events instantiated
		$this->bootstrap();
		\Foolz\Plugin\Hook::forge('foolz\plugin\plugin.install.'.$this->getJsonConfig('name'))
			->setObject($this)
			->execute();

		return $this;
	}

	/**
	 * Triggers the remove methods for the plugin
	 *
	 * @return  \Foolz\Plugin\Plugin
	 */
	public function remove()
	{
		// clear the hook since we might have an old one
		\Foolz\Plugin\Event::clear('foolz\plugin\plugin.remove.'.$this->getJsonConfig('name'));

		// execute the bootstrap to get the events instantiated
		$this->bootstrap();
		\Foolz\Plugin\Hook::forge('foolz\plugin\plugin.remove.'.$this->getJsonConfig('name'))
			->setObject($this)
			->execute();

		return $this;
	}

	/**
	 * Triggers the upgrade methods for the plugin. At this point the files have already changed..
	 *
	 * @return  \Foolz\Plugin\Plugin
	 */
	public function upgrade()
	{
		// clear the json data so we use the latest
		$this->clearJsonConfig();

		// clear the hook since we for sure have an old one
		\Foolz\Plugin\Event::clear('foolz\plugin\plugin.upgrade.'.$this->getJsonConfig('name'));

		// execute the bootstrap to get the events re-instantiated
		$this->bootstrap();

		// run the event
		\Foolz\Plugin\Hook::forge('foolz\plugin\plugin.upgrade.'.$this->getJsonConfig('name'))
			->setObject($this)
			// the PHP config holds the old revision
			->setParam('old_revision', $this->getConfig('extra.revision', 0))
			// the JSON config holds the new revision
			->setParam('new_revision', $this->getJsonConfig('extra.revision', 0))
			->execute();

		// update the PHP config file so it has the new revision
		$this->refreshConfig();

		return $this;
	}
}