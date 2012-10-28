<?php

namespace Foolz\Plugin;

/**
 * Automates loading of plugins
 *
 * @author   Foolz <support@foolz.us>
 * @package  Foolz\Plugin
 * @license  http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 */
class Loader
{
	/**
	 * The instances of the Loader class
	 *
	 * @var  \Foolz\Plugin\Loader[]
	 */
	protected static $instances = [];

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
	 * @var  boolean
	 */
	protected $reload = true;

	/**
	 * Construct registers the class to the autoloader
	 */
	public function __construct()
	{
		$this->register();
	}

	/**
	 * Creates or returns a named instance of Loader
	 *
	 * @param   string  $instance  The name of the instance to use or create
	 * @param   bool    $prepend   If the autoloader should be prepended
	 *
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

	/**
	 * Destroys a named instance and unregisters its autoloader
	 *
	 * @param  string  $instance  The name of the instance to use or create
	 */
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
		spl_autoload_register([$this, 'classLoader'], true, $prepend);
	}

	/**
	 * Unregisters the current object with spl_autoload_unregister
	 */
	protected function unregister()
	{
		spl_autoload_unregister([$this, 'classLoader']);
	}

	/**
	 * Class Autoloader function
	 *
	 * @param   string  $class  The name of the class to load
	 *
	 * @return  void|boolean  True if the class has been found, void otherwise
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
	 * @param   string  $class  The name of the class
	 * @param   string  $path   The path where the class can be found
	 *
	 * @return  \Foolz\Plugin\Loader  The current object
	 */
	public function addClass($class, $path)
	{
		$this->classes[$class] = $path;
		return $this;
	}

	/**
	 * Returns the path of the class
	 *
	 * @param   string  $class  The name of the class
	 *
	 * @return  string  The path to the class
	 * @throws  \OverflowException  If the class hasn't been declared
	 */
	public function getClassPath($class)
	{
		if ( ! isset($this->classes[$class]))
		{
			throw new \OverflowException;
		}

		return $this->classes[$class];
	}

	/**
	 * Removes a class from the autoloader
	 *
	 * @param   string  $class  The class to remove
	 *
	 * @return  \Foolz\Plugin\Loader  The current object
	 */
	public function removeClass($class)
	{
		unset($this->classes[$class]);
		return $this;
	}

	/**
	 * Adds a directory to the array of directories to search plugins in
	 *
	 * @param   string       $dir_name  If $dir is not set this sets both the name and the dir equal
	 * @param   null|string  $dir       The dir where to look for plugins
	 *
	 * @return  \Foolz\Plugin\Loader  The current object
	 * @throws  \DomainException      If the directory is not found
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
		$this->reload = true;

		return $this;
	}

	/**
	 * Removes a dir from the array of directories to search plugins in
	 * Unsets also all the plugins in that directory
	 *
	 * @param   string  $dir_name  The named directory
	 *
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
	public function find()
	{
		if ($this->plugins === null)
		{
			$this->plugins = array();
		}

		foreach ($this->dirs as $dir_name => $dir)
		{
			if ( ! isset($this->plugins[$dir_name]))
			{
				$this->plugins[$dir_name] = [];
			}

			$vendor_paths = $this->findDirs($dir);

			foreach ($vendor_paths as $vendor_name => $vendor_path)
			{
				$plugin_paths = $this->findDirs($vendor_path);

				foreach ($plugin_paths as $plugin_name => $plugin_path)
				{
					if ( ! isset($this->plugins[$dir_name][$vendor_name.'/'.$plugin_name]))
					{
						$plugin = new Plugin($plugin_path);
						$plugin->setLoader($this);
						$this->plugins[$dir_name][$vendor_name.'/'.$plugin_name] = $plugin;
					}
				}
			}
		}
	}

	/**
	 * Internal function to find all directories at the path
	 *
	 * @param   string  $path  The path to look into
	 *
	 * @return  array   The paths with as they the last part of the path
	 */
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
	 *
	 * @return  \Foolz\Plugin\Plugin[]  All the plugins or the plugins in the directory
	 * @throws  \OutOfBoundsException   If there isn't such a $dir_name set
	 */
	public function getAll($dir_name = null)
	{
		if ($this->reload === true)
		{
			$this->find();
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
	 * @param   string  $dir_name           The directory name where to find the plugin
	 * @param   string  $slug               The slug of the plugin
	 * @param   string  $fallback           The fallback theme if the first is not found
	 * @param   string  $fallback_dir_name  The dir name of the fallback if it's in another directory
	 *
	 * @return  \Foolz\Plugin\Plugin
	 * @throws  \OutOfBoundsException  if the theme doesn't exist and if the fallback wasn't found either
	 */
	public function get($dir_name, $slug, $fallback = null, $fallback_dir_name = null)
	{
		$plugins = $this->getAll();

		if ( ! isset($plugins[$dir_name][$slug]))
		{
			if (func_num_args() >= 3)
			{
				$this->get($fallback_dir_name !== null ? $fallback_dir_name : $dir_name, $fallback);
				return;
			}

			throw new \OutOfBoundsException('There is no such a plugin.');
		}

		return $plugins[$dir_name][$slug];
	}
}