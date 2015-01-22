<?php

namespace Foolz\Plugin;

/**
 * The Hook that runs the events appended to it
 *
 * @author Foolz <support@foolz.us>
 * @package Foolz\Plugin
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 */
class Hook
{
    /**
     * The hook key
     *
     * @var  null|string
     */
    protected $key = null;

    /**
     * The object the hook has been placed in, if any
     *
     * @var  null|object
     */
    protected $object = null;

    /**
     * The parameters to pass to the
     *
     * @var  array
     */
    protected $params = array();

    /**
     * The disabled keys
     *
     * @var  array
     */
    protected static $disabled = array();

    /**
     * Takes a hook key
     *
     * @param  string  $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Shorthand for PHP5.3 to concatenate on constructor
     *
     * @param   string  $key
     * @return  \Foolz\Plugin\Hook
     */
    public static function forge($key)
    {
        return new static($key);
    }

    /**
     * Enables the Hook key if it was disabled
     *
     * @param  string  $key
     */
    public static function enable($key)
    {
        static::$disabled = array_diff(static::$disabled, array($key));
    }

    /**
     * Disable the Hook key
     *
     * @param  string  $key
     */
    public static function disable($key)
    {
        static::$disabled[] = $key;
    }

    /**
     * Sets the object the hook has been placed in
     *
     * @param   object  $object
     * @return  \Foolz\Plugin\Hook
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Sets a parameter that will be passed to the events
     *
     * @param   string  $key
     * @param   mixed   $value
     * @return  \Foolz\Plugin\Hook
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * Set bulks of parameters
     *
     * @param   array  $arr
     * @return  \Foolz\Plugin\Hook
     */
    public function setParams(array $arr)
    {
        foreach ($arr as $key => $item) {
            $this->params[$key] = $item;
        }

        return $this;
    }

    /**
     * Executes the hook and cascades through all the events
     *
     * @return  \Foolz\Plugin\Result
     */
    public function execute()
    {
        $result = new Result($this->params, $this->object);

        if (in_array($this->key, static::$disabled)) {
            return $result;
        }

        $events = Event::getByKey($this->key);

        foreach ($events as $event) {
            $call = $event->getCall();

            // if we set an object, the call is a closure and the PHP version is at least 5.4...
            if ($this->object !== null && $call instanceof \Closure && version_compare(PHP_VERSION, '5.4.0') >= 0 && !defined('HHVM_VERSION')) {
                // ...bind the Closure's $this to the object
                $call = $call->bindTo($this->object, $this->object);
            }

            // users may not set the call, and that would be troubles
            if ($call !== null) {
                call_user_func($call, $result);
            }
        }

        return $result;
    }
}
