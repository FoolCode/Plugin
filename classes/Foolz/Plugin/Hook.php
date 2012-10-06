<?php

namespace Foolz\Plugin;

/**
 * The Hook that runs the events appended to it
 */
class Hook
{
	/**
	 * The hook key
	 *
	 * @var null|string
	 */
	protected $key = null;

	/**
	 * The object the hook has been placed in, if any
	 *
	 * @var null|object
	 */
	protected $object = null;

	/**
	 * The parameters to pass to the
	 *
	 * @var type
	 */
	protected $params = array();

	/**
	 * Takes a hook key
	 *
	 * @param string $key
	 */
	public function __construct($key)
	{
		$this->key = $key;
	}

	/**
	 * Shorthand for PHP5.3 to concatenate on constructor
	 *
	 * @param type $key
	 * @return \Foolz\Plugin\Hook
	 */
	public static function forge($key)
	{
		return new static($key);
	}

	/**
	 * Sets the object the hook has been placed in
	 *
	 * @param object $object
	 * @return \Foolz\Plugin\Hook
	 */
	public function setObject($object)
	{
		$this->object = $object;
		return $this;
	}

	/**
	 * Sets a parameter that will be passed to the events
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return \Foolz\Plugin\Hook
	 */
	public function setParam($key, $value)
	{
		$this->params[$key] = $value;
		return $this;
	}

	/**
	 * Set bulks of parameters
	 *
	 * @param array $arr
	 * @return \Foolz\Plugin\Hook
	 */
	public function setParams(array $arr)
	{
		foreach ($arr as $key => $item)
		{
			$this->params[$key] = $item;
		}

		return $this;
	}

	/**
	 * Executes the hook and cascades through all the events
	 *
	 * @return \Foolz\Plugin\Result
	 */
	public function execute()
	{
		$events = Event::getByKey($this->key);

		$result = new Result($this->params, $this->object);

		foreach ($events as $event)
		{
			$call = $event->getCall();

			// users may not set the call, and that would be troubles
			if ($call !== null)
			{
				call_user_func($call, $result);
			}
		}

		return $result;
	}
}