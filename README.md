Foolz PHP Plugin system
=======================

A very complete plugin system to let you claim that your application supports plugins!

You will need PHP 5.3 for this to work. You can install it through [Composer](http://getcomposer.org/) and [Packagist](https://packagist.org/packages/foolz/plugin).

[![Build Status](https://secure.travis-ci.org/FoolRulez/Plugin.png)](http://travis-ci.org/FoolRulez/Plugin)

## Components

* __Hooks__

	Place them in your code to execute events. Unlike several other packages, these Hooks allow interacting with the data and can hold the scope of the class.
* __Events__

	The events happen when a Hook with the same key is encountered. Events accept static methods and Closures, as well as a priority, because _you can stack them and have them passing variables to each other_.
* __Result__

	The object that Hooks and Events share, and return. They keep your code clean from mysterious function parameters and even more confusing return values.
* __Plugin__

	The internal package handler. _Plugins are actually Composer packages._ Use the bootstrap file to create new events and use the install/uninstall/upgrade hooks.

* __Loader__

	The packages utility. It looks into the folders you tell it to, finds plugins, loads them and give you the arrays.

What will you have to do? You must use the Loader class to create your own administration panel and run the plugins you choose to run. Since it doesn't have any database bind (or any dependency at all), you must create an enabled/disabled system yourself.

Some examples follow.

You can go in-depth with these explanations with the following pages:

* [Hooks, Events and Results](blob/master/hooks_events_results.md)
* [Plugin packages](blob/master/plugin_packages.md)

## Hooks and Events

The most basic part of the package. You can use Hook and Event anywhere in your code, not only in the plugins.

```php
<?php

use \Foolz\Plugin\Hook as Hook;
use \Foolz\Plugin\Event as Event;

// define an Event
Event::forge('triggerOnThis')->setCall(function($result){
	$result->set(123);
})->priority(2);

// the Hook triggers it
$result = Hook::forge('triggerOnThis')->execute();

// echoes 123
echo $result->get();
```

## Result

We coded the result package to avoid confusion with cascading Events. This is why Events only get one parameter, that we use to call `$result`.

As this is where most of the mistakes are made, like fetching parameters that haven't been set, any unset parameter fetched or unset result without explicit fallback will cause an exception.

Example:
```php
<?php

// define an Event
Event::forge('triggerOnThis')->setCall(function($result){
	$increment = $result->getParam('increment')
	$increment++;
	$result->setParam($increment);
	$result->set($increment);
});

// define another Event editing the parameters with lower priority (lower number is higher priority, default is 5)
Event::forge('triggerOnThis')->setCall(function($result){
	$increment = $result->getParam('increment')
	$increment++;
	$result->set($increment);
})->priority(8);

// the Hook triggers it
$result = Hook::forge('triggerOnThis')
	->setParam('increment', 0)
	->execute();

// echoes 2 (we increased the number in each event)
echo $result->get();

// echoes 1 (we edited the parameter only in the first event)
echo $result->getParam('increment');
```

## Plugins

The folder structure must be: `plugins_folder/vendor_name/plugin_name`.

A plugin is made of at least one file: the composer.json file. That is right: __the configuration file is a composer.json file__. You can add any extra information you need in the [extra fields](http://getcomposer.org/doc/04-schema.md#extra), like in example a pretty `name` for the plugin.

The other file you really want is the `bootstrap.php` file. In the bootstrap you will have four events to add:

* `\foolz\plugin\plugin.execute.vendor_name/plugin_name`
* `\foolz\plugin\plugin.install.vendor_name/plugin_name`
* `\foolz\plugin\plugin.uninstall.vendor_name/plugin_name`
* `\foolz\plugin\plugin.update.vendor_name/plugin_name`

The last one sets two parameters: `old_revision` and `new_revision`. These are based on the eventual `extra.revision`, and should be used to manage migrations.

Here's an example of how would you load the plugins you choose to run:

```php
<?php

$loader = Loader::forge()->setDir('main', '/path/to/plugins/');

$enabled_plugins = array('banners', 'skynet');

foreach ($loader->getPlugins('main') as $plugin)
{
	if (in_array($plugin->getConfig('slug'), $enabled_plugins)
	{
		$plugin->execute();
	}
}
```