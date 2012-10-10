## Plugins

Since we want to claim that our application supports plugins, we absolutely need a bit of plugin management.

As we already have Hooks and Events, we can freely use them in the plugins. We're set for building something interesting.

## Structure

Plugins are composed by at least two files: `composer.json` and `bootstrap.php`. A third file may be automatically created for performance purposes, called `composer.php` containing the `composer.json` data. You can use the directory for anything else.

You will be able to choose multiple named directories stores the plugins. In general, this is the structure: `plugins_folder/vendor_name/plugin_name` In example, one of our plugins would be in `plugins/foolz/fake`.

### composer.json

In example: `plugins_folder/vendor_name/plugin_name/composer.json`

You can describe the plugin with the [structure of the composer.json file](http://getcomposer.org/doc/04-schema.md): it contains all that we need to describe a package. We make one single addition to improve functionality:

	{
		"extra" : {
			"revision" : 0
		}
	}

This allows setting a revision, used for database migrations. It's not compulsory to add this line to the composer.json, only add it when you need migrations. Use the `extra` section of `composer.json` to set your own plugin data. The Plugin class will be able to read that data.

### bootstrap.php

In example: `plugins_folder/vendor_name/plugin_name/bootstrap.php`

This will be run every time you `execute()` the plugin. If you have a plugin called `foolz/fake`, it's structure may be the following:

```php
<?php
	// execute
	\Event::forge('\foolz\plugin\plugin.execute.foolz/fake')
		->setCall(function($result){

		});

	// install
	\Event::forge('\foolz\plugin\plugin.install.foolz/fake')
		->setCall(function($result){

		});

	// uninstall
	\Event::forge('\foolz\plugin\plugin.uninstall.foolz/fake')
		->setCall(function($result){

		});

	// upgrade
	\Event::forge('\foolz\plugin\plugin.upgrade.foolz/fake')
		->setCall(function($result){
			$old_revision = $result->getParam('old_revision');
			$new_revision = $result->getParam('new_revision');
		});
```

It's not necessary to add all of them to `bootstrap.php`. Inside the closures you should insert your bootstrapping code for the plugin, and that should be mainly two things:

* Setting Events
* Setting Classes for autoloading

At the time of writing this, the package doesn't support PSR-0 loading. You can still use the class autoloading functions to define the location of your classes. This will keep working even after we add PSR-0 autoloading.

The __install__ event is meant to migrate to the latest revision of the database and file schema. Keep the install always to the latest version so it doesn't require migrations. The __upgrade__ event gets two parameters: `old_revision` and `new_revision`. You can use these to determine which migration actions the plugin should take. The __uninstall__ event is meant to revert the changes made by the plugin to the system.

## Loader

This class gives easy access to the arrays of plugins.

#### new Loader()

Instantiation.

#### Loader::forge($instance = 'default')

Creates or returns an instance of Loader.

* string _$instance_ - The name of the instance

_Chainable_

#### Loader::destroy($instance = 'default')

Destroys an instance

* string _$instance_ - The name of the instance

#### ->addDir($dir_name, $dir = null)

Sets a named director where to look for the plugins.

* string _$dir\_name_ - An unique name for the directory. If only this is declared it should be the path. $dir_name and $dir will then be the same.
* string _$dir_ - The path to the plugin folder

_Chainable_

#### ->removeDir($dir_name)

Removes a dir in which plugins are looked into and unsets all the plugins loaded from that dir

* string _$dir\_name - The named dir to remove

#### ->getPlugins($dir_name = null)

Returns all the plugins.

* string _$dir\_name_ - If set it will only return the plugins from the named directory

__Returns:__ an associative array of \Foolz\Plugin\Plugin with the dir name as first key and the plugin name as second. Only the plugin name as key if the dir name is set.

#### ->getPlugin($dir_name, $slug)

Returns the plugin.

* string _$dir\_name_ - The named dir where the plugin is found
* string _$slug_ - The name of the plugin


----


## Plugin

#### ->addClass($class, $path)

Sets a class path for the autoloader.

* string _$class_ - The class name
* string _$path_ - The path to the class

#### ->remove









