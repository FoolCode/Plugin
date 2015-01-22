<?php

namespace Foolz\Plugin;

/**
 * An event is an action that is run on a Hook with the same key
 *
 * @author  Foolz <support@foolz.us>
 * @package  Foolz\Plugin
 * @license  http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 */
class Event
{
    /**
     * Array of all the registered events
     *
     * @var  array  the key is the Hook key, value is the Event object
     */
    protected static $events = array();

    /**
     * The Hook key
     *
     * @var  null|string|array
     */
    protected $key = null;

    /**
     * Priority to order the execution of plugins on the same hook. Lower runs earlier, negative allowed.
     *
     * @var  int
     */
    protected $priority = 5;

    /**
     * The string to call a static class method or a Closure
     *
     * @var  null|string|Closure
     */
    protected $callable = null;

    /**
     * Takes the Hook key and stores the object in the static::$events array
     *
     * @param  string|array  $key  the key or the array of keys
     */
    public function __construct($key)
    {
        $this->key = $key;

        if (is_array($key)) {
            foreach ($key as $k) {
                static::$events[$k][] = $this;
            }
        } else {
            static::$events[$key][] = $this;
        }
    }

    /**
     * Shorthand for the construct for PHP 5.3 to allow chaining
     *
     * @param  string  $key
     * @return  \Foolz\Plugin\Event
     */
    public static function forge($key)
    {
        return new static($key);
    }

    /**
     * Removes all the events with the key
     *
     * @param  string  $key
     */
    public static function clear($key)
    {
        unset(static::$events[$key]);
    }

    /**
     * Returns all the events ordered by ascending priority number
     *
     * @param  string  $key
     * @return  array  empty if no hooks present, ordered by ascending priority number
     */
    public static function getByKey($key)
    {
        if (!isset(static::$events[$key])) {
            return array();
        }

        $events = static::$events[$key];

        // sort by ascending priority
        usort($events, function($a, $b) { return $a->getPriority() - $b->getPriority(); });

        return $events;
    }

    /**
     * Returns the priority
     *
     * @return  int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Sets the priority
     *
     * @param   int  $priority
     * @return  \Foolz\Plugin\Event
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Gets the method or the Closure to run
     *
     * @return  string|Callable
     */
    public function getCall()
    {
        return $this->callable;
    }

    /**
     * Sets the method or Closure to run
     *
     * @param  string|Closure  $callable
     * @return  \Foolz\Plugin\Event
     */
    public function setCall($callable)
    {
        $this->callable = $callable;

        return $this;
    }
}
