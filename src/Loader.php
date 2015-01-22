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
     * @return  \Foolz\Plugin\Plugin[]  All the plugins or the plugins in the directory
     * @throws  \OutOfBoundsException   If there isn't such a $dir_name set
     */
    public function getAll()
    {
        return parent::getAll();
    }

    /**
     * Gets a single plugin object
     *
     * @param   string  $slug               The slug of the plugin
     *
     * @return  \Foolz\Plugin\Plugin
     * @throws  \OutOfBoundsException  if the plugin doesn't exist
     */
    public function get($slug)
    {
        return parent::get($slug);
    }
}
