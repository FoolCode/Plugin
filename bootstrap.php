<?php

/**
 * Bootstrap for FuelPHP use only
 */

\Autoloader::add_classes(array(
	'Foolz\\Plugin\\Hook' => __DIR__.'/classes/Foolz/Plugin/Hook.php',
	'Foolz\\Plugin\\Event' => __DIR__.'/classes/Foolz/Plugin/Event.php',
	'Foolz\\Plugin\\Result' => __DIR__.'/classes/Foolz/Plugin/Result.php',
));