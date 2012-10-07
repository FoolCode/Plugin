<?php

namespace Foolz\Plugin;

use Composer\Script\Event as ComposerEvent;

class Composer
{
	/**
	 * Installation routine that should be run by composer
	 */
	public static function install(ComposerEvent $event)
	{
		$package = $event->getOperation()->getPackage();

		// continue if it's not a plugin
		if ($package->getSourceType() !== 'foolz-plugin')
		{
			return;
		}

		// add the package to the composer list of plugins
		$loader_conf_path = __DIR__.'/../../../resources/loader.php';
		$loader_conf = include $loader_conf_path;
		$loader_conf['composer'][] = $package->getName();
		Util::saveArrayToFile($loader_conf_path, $loader_conf);

		$loader = Loader::forge('composer');
		$plugin = $loader->getPlugin('composer', $package->getName());
		$plugin->install();
	}

	public static function update(ComposerEvent $event)
	{
		$package = $event->getOperation()->getPackage();

		// continue if it's not a plugin
		if ($package->getSourceType() !== 'foolz-plugin')
		{
			return;
		}

		$loader = Loader::forge('composer');
		$plugin = $loader->getPlugin('composer', $package->getName());
		$plugin->update();
	}

	public static function uninstall(ComposerEvent $event)
	{
		$package = $event->getOperation()->getPackage();

		// continue if it's not a plugin
		if ($package->getSourceType() !== 'foolz-plugin')
		{
			return;
		}

		$loader = Loader::forge('composer');
		$plugin = $loader->getPlugin('composer', $package->getName());
		$plugin->uninstall();

		// add the package to the composer list of plugins
		$loader_conf_path = __DIR__.'/../../../resources/loader.php';
		$loader_conf = include $loader_conf_path;

		$loader_conf = array_diff($loader_conf, array($package->getName()));
		Util::saveArrayToFile($loader_conf_path, $loader_conf);
	}
}