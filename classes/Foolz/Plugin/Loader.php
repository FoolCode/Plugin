<?php

namespace Foolz\Plugin;

/**
 * Automates loading of plugins
 *
 * @author   Foolz <support@foolz.us>
 * @package  Foolz\Plugin
 * @license  http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 */
class Loader extends \Foolz\Package\Loader
{
	/**
	 * The type of package in use. Can be in example 'theme' or 'plugin'
	 * Override this to change type of package
	 *
	 * @var  string
	 */
	protected $type_name = 'plugin';

	/**
	 * The class into which the resulting objects are created.
	 * Override this, in example Foolz\Plugin\Plugin or Foolz\Theme\Theme
	 *
	 * @var  string
	 */
	protected $type_class = 'Foolz\Plugin\Plugin';

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
		return parent::getAll($dir_name);
	}

	/**
	 * Gets a single plugin object
	 *
	 * @param   string  $dir_name           The directory name where to find the plugin
	 * @param   string  $slug               The slug of the plugin
	 *
	 * @return  \Foolz\Plugin\Plugin
	 * @throws  \OutOfBoundsException  if the plugin doesn't exist
	 */
	public function get($dir_name, $slug)
	{
		return parent::get($dir_name, $slug);
	}
}