<?php

/**
 * Bootstrap for FuelPHP use only
 */

\Autoloader::add_classes(array(
	'Foolz\\Plugin\\Void' => __DIR__.'/classes/Foolz/Plugin/Void.php',
	'Foolz\\Plugin\\Util' => __DIR__.'/classes/Foolz/Plugin/Util.php',
	'Foolz\\Plugin\\Loader' => __DIR__.'/classes/Foolz/Plugin/Loader.php',
	'Foolz\\Plugin\\Plugin' => __DIR__.'/classes/Foolz/Plugin/Plugin.php',
	'Foolz\\Plugin\\Hook' => __DIR__.'/classes/Foolz/Plugin/Hook.php',
	'Foolz\\Plugin\\Event' => __DIR__.'/classes/Foolz/Plugin/Event.php',
	'Foolz\\Plugin\\Result' => __DIR__.'/classes/Foolz/Plugin/Result.php',
));