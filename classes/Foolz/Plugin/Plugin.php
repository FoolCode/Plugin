<?php

namespace Foolz\Plugin;

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
	 * The config of the plugin
	 *
	 * @var array
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
		$this->dir = $dir;
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
	 * Runs the bootstrap of the plugin
	 */
	public function execute()
	{
		$bootstrap = $this->getConfig('files.bootstrap');
		include $this->getDir().$bootstrap;
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
	public function getJsonConfig()
	{
		$file = $this->getDir().'config.json';

		if ( ! file_exists($file))
		{
			throw new \DomainException;
		}

		$config = json_decode($file, true);

		if ($config === null)
		{
			throw new \DomainException;
		}

		return $config;
	}

	/**
	 * Converts the JSON to a PHP config to improve speed
	 *
	 * @return \Foolz\Plugin\Plugin
	 */
	public function jsonToConfig()
	{
		$config = $this->getJsonConfig();

		$this->saveConfig($config);
		return $this;
	}

	/**
	 *
	 * @param type $array
	 * @return \Foolz\Plugin\Plugin
	 */
	public function saveConfig($array)
	{
		$content = "<?php \n".
		"return ".var_export($array, true);

		file_put_contents($this->getDir().'config.php', $content);
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
		$throw = false;

		// if the user doesn't pass the fallback it must throw an exception if the value is not found
		if (func_num_args() !== 2)
		{
			$throw = true;
		}

		$php_file = $this->getDir().'config.php';

		if (file_exists($php_file) === false)
		{
			$this->jsonToConfig();
		}

		$config = include $php_file;

		if ($section === null)
		{
			return $config;
		}

		// get the section with the dot separated string
		$sections = explode('.', $section);
		$current = $config;
		foreach ($sections as $key)
		{
			if (isset($current[$key]))
			{
				$current = $current[$key];
			}
			else
			{
				if ($throw)
				{
					throw new \DomainException;
				}

				return $fallback;
			}
		}
	}

	/**
	 * Triggers the install methods for the plugin
	 */
	public function install()
	{
		$install = $this->getConfig('files.install', false);

		if ($install === false)
		{
			return;
		}

		include $this->getDir().$install;
	}

	/**
	 * Triggers the remove methods for the plugin
	 */
	public function remove()
	{
		$remove = $this->getConfig('files.remove', false);

		if ($remove === false)
		{
			return;
		}

		include $this->getDir().$remove;
	}

	/**
	 * Triggers the upgrade methods for the plugin
	 */
	public function upgrade()
	{
		$upgrade = $this->getConfig('files.upgrade', false);

		if ($upgrade === false)
		{
			return;
		}

		include $this->getDir().$upgrade;
	}
}