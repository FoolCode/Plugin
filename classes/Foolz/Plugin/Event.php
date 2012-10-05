<?php

namespace Foolz\Plugin;

class Event
{
	/**
	 * Array of all the registered events
	 *
	 * @var array the key is the Hook key, value is the Event object
	 */
	protected static $events = array();

	/**
	 * The Hook key
	 *
	 * @var null|string
	 */
	protected $key = null;

	/**
	 * Priority to order the execution of plugins on the same hook. Lower runs earlier, negative allowed.
	 *
	 * @var int
	 */
	protected $priority = 5;

	/**
	 * The string to call a static class method or a Closure
	 *
	 * @var null|string|Closure
	 */
	protected $callable = null;

	/**
	 * Takes the Hook key and stores the object in the static::$events array
	 *
	 * @param string $key
	 */
	public function __construct($key)
	{
		$this->key = $key;
		static::$events[$key][] = $this;
	}

	/**
	 * Returns all the events ordered by ascending priority number
	 *
	 * @param string $key
	 * @return array empty if no hooks present, ordered by ascending priority number
	 */
	public static function getByKey($key)
	{
		if ( ! isset(static::$events[$key]))
		{
			return array();
		}

		$events = static::$events[$key];

		// sort by ascending priority
		usort($events, function($a, $b) { return $a->priority - $b->priority; });

		return $events;
	}

	/**
	 * Sets the priority
	 *
	 * @param int $priority
	 */
	public function setPriority($priority)
	{
		$this->priority = $priority;
	}

	/**
	 * The method or Closure to run
	 *
	 * @param string|Closure $callable
	 */
	public function call($callable)
	{
		$this->callable = $callable;
	}
}