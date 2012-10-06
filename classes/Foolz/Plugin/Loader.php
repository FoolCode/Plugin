<?php

namespace Foolz\Plugin;

class Loader
{
	/**
	 * Directory where support directories like cache are located
	 *
	 * @var string
	 */
	protected static $resource_dir = null;

	/**
	 * Files to delete on class destruct
	 *
	 * @var type
	 */
	protected $to_delete = array();

	/**
	 * The instances of the Loader class
	 *
	 * @var array
	 */
	protected static $instances = array();

	/**
	 * The dirs in which to look for plugins
	 *
	 * @var null|array
	 */
	protected $dirs = null;

	/**
	 * The classes for the autoloader
	 *
	 * @var null|array
	 */
	protected $classes = null;

	/**
	 * The plugins found
	 *
	 * @var null|array as first key the dir name, as second key the slug
	 */
	protected $plugins = null;

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
	 * @param string $instance
	 * @param bool $prepend if the autoloader should be prepended
	 * @return \Foolz\Plugin\Loader
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
	 * Removes the files tagged for deletion
	 */
	protected function __destruct()
	{
		foreach ($this->to_delete as $delete)
		{
			if ( ! file_exists($delete))
			{
				continue;
			}

			if (is_dir($delete))
			{
				$this->flushDir($delete);
			}

			unlink($delete);
		}
	}

	/**
	 * Sets a file for deletion on object destruct
	 *
	 * @param string $path
	 * @return \Foolz\Plugin\Loader
	 */
	protected function setForDeletion($path)
	{
		$this->to_delete[] = $path;
		return $this;
	}

	/**
	 * Registers the current object with spl_autoload_register
	 *
	 * @param bool $prepend if the class loader should run first, would allow overriding classes
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
	 * @param string $class
	 * @return void|bool
	 * @throws \OutOfBoundsException if the class doesn't exist
	 */
	public function classLoader($class)
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
	 * @param string $class
	 * @param string
	 * @return \Foolz\Plugin\Loader
	 */
	public function addClass($class, $path)
	{
		$this->classes[$class] = $path;
		return $this;
	}

	/**
	 * Removes a class from the autoloader
	 *
	 * @param string $class
	 * @return \Foolz\Plugin\Loader
	 */
	public function removeClass($class)
	{
		unset($this->classes[$class]);
		return $this;
	}

	/**
	 * Adds a directory to the array of directories to search plugins in
	 *
	 * @param string $dir_name if $dir is not set this sets both the name and the dir equal
	 * @param null|string $dir the dir where to look for plugins
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
			throw new \DomainException;
		}

		$this->dirs[$dir_name] = $dir;
		return $this;
	}

	/**
	 * Removes a dir from the array of directories to search plugins in
	 *
	 * @param string $dir_name
	 * @return \Foolz\Plugin\Loader
	 */
	public function removeDir($dir_name)
	{
		unset($this->dirs[$dir_name]);
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

			$fp = opendir($dir);

			while (false !== ($file = readdir($fp)))
			{
				// Remove '.', '..'
				if (in_array($file, array('.', '..')))
				{
					continue;
				}

				$filepath = $path.'/'.$file;

				if (is_dir($filepath))
				{
					if ( ! isset($this->plugins[$dir_name][$file]))
					{
						$this->plugins[$dir_name][$file] = new \Foolz\Plugin\Plugin($this, $filepath);
					}
				}
			}

			closedir($fp);
		}
	}

	/**
	 * Gets all the plugins or the plugins from the directory
	 *
	 * @param null|string $dir_name if specified it gets only a group of plugins
	 * @return array
	 * @throws \OutOfBoundsException
	 */
	public function getPlugins($dir_name = null)
	{
		if ($this->plugins === null)
		{
			$this->findPlugins();
		}

		if ($dir_name === null)
		{
			return $this->plugins;
		}

		if ( ! isset($this->plugins[$dir_name]))
		{
			throw new \OutOfBoundsException;
		}

		return $this->plugins[$dir_name];
	}

	/**
	 * Gets a single plugin object
	 *
	 * @param string $dir_name
	 * @param string $slug
	 */
	public function getPlugin($dir_name, $slug)
	{
		$plugins = $this->getPlugins();

		if ( ! isset($plugins[$dir_name][$slug]))
		{
			throw new \OutOfBoundsException;
		}

		return $plugins[$dir_name][$slug];
	}

	/**
	 * Fetches a plugin and prepares it for use
	 *
	 * @param string $dir_name Named directory in which to put the plugin
	 * @param string $url Url to wget from
	 */
	public function downloadPlugin($dir_name, $slug, $url)
	{
		$download = file_get_contents($url);

		// serious things may break if this is 0 length
		if (strlen($slug) == 0)
		{
			throw new \InvalidArgumentException;
		}

		$base_path = static::$resource_dir.'cache/'.$slug.'/';
		$this->setForDeletion($base_path);

		if ( ! file_exists($base_path))
		{
			mkdir($base_path);
		}

		file_put_contents($base_path.'plugin.zip', $download);

		$zip = new \ZipArchive();
		$zip->open($base_path.'plugin.zip');
		mkdir($base_path.'extracted/');
		$zip->extractTo($base_path.'extracted/');
		$zip->close();

		if ( ! file_exists($base_path.'extracted/config.json'))
		{
			throw new \UnexpectedValueException;
		}

		// use the slug defined in the package to make sure it goes in the correct folder
		$json = json_decode($base_path.'extracted/config.json', true);

		if ( ! isset($json['slug']))
		{
			throw new \UnexpectedValueException;
		}

		copy($base_path.'extracted/', $this->dirs[$dir_name].$json['slug'].DIRECTORY_SEPARATOR);

		$this->flushDir($base_path);
		unlink($base_path);
	}

	/**
	 * Removes the plugin directory. It doesn't uninstall the plugin.
	 *
	 * @param string $dir_name Named directory where the plugin is located
	 * @param type $slug
	 */
	public function removePlugin($dir_name, $slug)
	{
		$plugin = $this->getPlugin($dir_name, $slug);
		$this->flushDir($plugin->getDir());
		unlink($plugin->getDir());
	}

	/**
	 * Empties a directory
	 *
	 * @param   string  $path
	 * @return  boolean
	 *
	 * @since  2.0.0
	 */
	protected function flushDir($path)
	{
		$fp = opendir($path);

		while (false !== ($file = readdir($fp)))
		{
			// Remove '.', '..'
			if (in_array($file, array('.', '..')))
			{
				continue;
			}

			$filepath = $path.'/'.$file;

			if (is_dir($filepath))
			{
				$this->flushDir($filepath);

				// removing dir here won't remove the root dir, just as we want it
				rmdir($filepath);
				continue;
			}
			else if (is_file($filepath))
			{
				unlink($filepath);
			}
		}

		closedir($fp);

		return true;
	}
}