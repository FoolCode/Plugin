<?php

namespace Foolz\Plugin;

/**
 * Automates loading of plugins, download and removal
 *
 * @author   Foolz <support@foolz.us>
 * @package  Foolz\Plugin
 * @license  http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 */
class Loader
{
	/**
	 * Directory where support directories like cache are located
	 *
	 * @var  string
	 */
	protected static $resource_dir = null;

	/**
	 * The instances of the Loader class
	 *
	 * @var  array
	 */
	protected static $instances = array();

	/**
	 * The dirs in which to look for plugins
	 *
	 * @var  null|array
	 */
	protected $dirs = null;

	/**
	 * The classes for the autoloader
	 *
	 * @var  null|array
	 */
	protected $classes = null;

	/**
	 * The plugins found
	 *
	 * @var  null|array  as first key the dir name, as second key the slug
	 */
	protected $plugins = null;

	/**
	 * Tells if plugins should be reloaded
	 *
	 * @var  bool
	 */
	protected $plugins_reload = true;

	/**
	 * Construct registers the class to the autoloader
	 */
	public function __construct()
	{
		if (static::$resource_dir === null)
		{
			static::$resource_dir = __DIR__.'/../../../resources/cache/';
		}

		$this->register();
	}

	/**
	 * Creates or returns a named instance of Loader
	 *
	 * @param   string  $instance
	 * @param   bool    $prepend   if the autoloader should be prepended
	 * @return  \Foolz\Plugin\Loader
	 */
	public static function forge($instance = 'default', $prepend = false)
	{
		if ( ! isset(static::$instances[$instance]))
		{
			return static::$instances[$instance] = new static($prepend);
		}

		return static::$instances[$instance];
	}

	public static function destroy($instance = 'default')
	{
		$obj = static::$instances[$instance];
		$obj->unregister();
		unset(static::$instances[$instance]);
	}

	/**
	 * Registers the current object with spl_autoload_register
	 *
	 * @param  bool  $prepend  if the class loader should run first, would allow overriding classes
	 */
	protected function register($prepend = false)
	{
		spl_autoload_register(array($this, 'classLoader'), true, $prepend);
	}

	/**
	 * Unregisters the current object with spl_autoload_unregister
	 */
	protected function unregister()
	{
		spl_autoload_unregister(array($this, 'classLoader'));
	}

	/**
	 * Class Autoloader function
	 *
	 * @param   string  $class
	 * @return  void|bool
	 */
	public function classLoader($class, $psr = false)
	{
		if (isset($this->classes[$class]))
		{
			include $this->classes[$class];
			return true;
		}
	}

	/**
	 * Adds a class to the autoloader
	 *
	 * @param   string  $class
	 * @param   string
	 * @return  \Foolz\Plugin\Loader
	 */
	public function addClass($class, $path)
	{
		$this->classes[$class] = $path;
		return $this;
	}

	/**
	 * Returns the path of the class
	 *
	 * @param   string  $class
	 * @return  string
	 */
	public function getClassPath($class)
	{
		return $this->classes[$class];
	}

	/**
	 * Removes a class from the autoloader
	 *
	 * @param   string  $class
	 * @return  \Foolz\Plugin\Loader
	 */
	public function removeClass($class)
	{
		unset($this->classes[$class]);
		return $this;
	}

	/**
	 * Adds a directory to the array of directories to search plugins in
	 *
	 * @param  string       $dir_name  if $dir is not set this sets both the name and the dir equal
	 * @param  null|string  $dir       the dir where to look for plugins
	 * @return \Foolz\Plugin\Loader
	 */
	public function addDir($dir_name, $dir = null)
	{
		if ($dir === null)
		{
			// if $dir is not specified, we use $dir_name as both $dir and $dir_name
			$dir = $dir_name;
		}

		if ( ! is_dir($dir))
		{
			throw new \DomainException('Directory not found.');
		}

		$this->dirs[$dir_name] = rtrim($dir,'/').'/';

		// set the flag to reload plugins on demand
		$this->plugins_reload = true;

		return $this;
	}

	/**
	 * Removes a dir from the array of directories to search plugins in
	 * Unsets also all the plugins in that directory
	 *
	 * @param   string  $dir_name
	 * @return  \Foolz\Plugin\Loader
	 */
	public function removeDir($dir_name)
	{
		unset($this->dirs[$dir_name]);
		unset($this->plugins[$dir_name]);
		return $this;
	}

	/**
	 * Looks for plugins in the specified directories and creates the objects
	 */
	public function findPlugins()
	{
		if ($this->plugins === null)
		{
			$this->plugins = array();
		}

		foreach ($this->dirs as $dir_name => $dir)
		{
			if ( ! isset($this->plugins[$dir_name]))
			{
				$this->plugins[$dir_name] = array();
			}

			$vendor_paths = $this->findDirs($dir);

			foreach ($vendor_paths as $vendor_name => $vendor_path)
			{
				$plugin_paths = $this->findDirs($vendor_path);

				foreach ($plugin_paths as $plugin_name => $plugin_path)
				{
					if ( ! isset($this->plugins[$dir_name][$vendor_name.'/'.$plugin_name]))
					{
						$plugin = new \Foolz\Plugin\Plugin($plugin_path);
						$plugin->setLoader($this);
						$this->plugins[$dir_name][$vendor_name.'/'.$plugin_name] = $plugin;
					}
				}
			}
		}
	}

	protected function findDirs($path)
	{
		$result = array();
		$fp = opendir($path);

		while (false !== ($file = readdir($fp)))
		{
			// Remove '.', '..'
			if (in_array($file, array('.', '..')))
			{
				continue;
			}

			if (is_dir($path.'/'.$file))
			{
				$result[$file] = $path.'/'.$file;
			}
		}

		closedir($fp);

		return $result;
	}

	/**
	 * Gets all the plugins or the plugins from the directory
	 *
	 * @param   null|string  $dir_name  if specified it gets only a group of plugins
	 * @return  array
	 * @throws  \OutOfBoundsException
	 */
	public function getPlugins($dir_name = null)
	{
		if ($this->plugins_reload === true)
		{
			$this->findPlugins();
		}

		if ($dir_name === null)
		{
			return $this->plugins;
		}

		if ( ! isset($this->plugins[$dir_name]))
		{
			throw new \OutOfBoundsException('There is no such a directory.');
		}

		return $this->plugins[$dir_name];
	}

	/**
	 * Gets a single plugin object
	 *
	 * @param   string  $dir_name
	 * @param   string  $slug
	 * @return  \Foolz\Plugin\Plugin
	 * @throws  \OutOfBoundsException
	 */
	public function getPlugin($dir_name, $slug)
	{
		$plugins = $this->getPlugins();

		if ( ! isset($plugins[$dir_name][$slug]))
		{
			throw new \OutOfBoundsException('There is no such a plugin.');
		}

		return $plugins[$dir_name][$slug];
	}
}