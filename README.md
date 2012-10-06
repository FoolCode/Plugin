Foolz PHP Plugin system
=======================

A very complete plugin system to let you claim that your application supports plugins!

You will need PHP 5.3 for this to work. You can install it through composer.

## What doesn't it do

* It doesn't give an administration panel, just the API to manage the plugins
* You must load the plugins early in your application yourself (`->executeAll()` if you're lazy).

## What does it do!

##### Hooks and Events

Hooks are placed in your code where you want an Event to run.

You can have multiple events running on the same hook, and they will cascade correctly with their priority number

To interact with the result, you will use the Result methods.

Example:
```php
<?php

use \Foolz\Plugin\Hook as Hook;
use \Foolz\Plugin\Event as Event;

// define an Event
(new Event('triggerOnThis'))->setCall(function($result){
	$result->set(123);
})->priority(2);

// the Hook triggers it
$result = (new Hook('triggerOnThis'))->execute();

// echoes 123
echo $result->get();
```

Notice that if you can't use the PHP 5.4 concatenation on instantiation, you can use the `Event::forge()` and `Hook::forge()` static methods instead, that do exactly the same.

##### Result

The Result is passed to Events as only parameter. It contains the associative array of parameters set by the hook and the result.

Example:
```php
<?php

// define an Event
(new Event('triggerOnThis'))->setCall(function($result){
	$increment = $result->getParam('increment')
	$increment++;
	$result->setParam($increment);
	$result->set($increment);
});

// define another Event editing the parameters with lower priority (lower number is higher priority, default is 5)
(new Event('triggerOnThis'))->setCall(function($result){
	$increment = $result->getParam('increment')
	$increment++;
	$result->set($increment);
})->priority(8);

// the Hook triggers it
$result = (new Hook('triggerOnThis'))
	->setParam('increment', 0)
	->execute();

// echoes 2 (we increased the number in each event)
echo $result->get();

// echoes 1 (we edited the parameter only in the first event)
echo $result->getParam('increment');
```

## Plugins

A plugin system wouldn't have a meaning without the portable packages that modify how the system works.

The plugin system is composed by a Loader class and Plugin class. In short, do the following to run your plugins (selectively):

```php
<?php

$loader = new Loader()->setDir('main', '/path/to/plugins/');

$enabled_plugins = array('banners', 'skynet');

foreach ($loader->getPlugins('main') as $plugin)
{
	if (in_array($plugin->getConfig('slug'), $enabled_plugins)
	{
		$plugin->execute();
	}
}
```

The Loader class is of course meant to be used with your administration system to create a dynamic system where you can install, enable, disable, remove and upgrade the plugins.