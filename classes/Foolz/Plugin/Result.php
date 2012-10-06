<?php

namespace Foolz\Plugin;

/**
 * The result that is cascaded through events on the same key and returned by the hook
 */
class Result
{
	/**
	 * The parameters (which can be modified with setParam())
	 *
	 * @var array as key the parameter key
	 */
	protected $params = array();

	/**
	 * The original parameters (can't be modified)
	 *
	 * @var array as key the parameter key
	 */
	protected $params_original = array();

	/**
	 * The object in which the Hook runs
	 *
	 * @var object
	 */
	protected $object = null;

	/**
	 * The result
	 *
	 * @var mixed
	 */
	protected $result = null;

	/**
	 * Sets the parameters and in case it's available the object
	 *
	 * @param array $params
	 * @param null|object $object
	 */
	public function __construct(array $params = array(), $object = null)
	{
		$this->params = $this->params_original = $params;
		$this->object = $object;
	}

	/**
	 * Returns the result
	 *
	 * @return mixed
	 */
	public function get()
	{
		return $this->result;
	}

	/**
	 * Sets the result
	 *
	 * @param type $val
	 * @return \Foolz\Plugin\Result
	 */
	public function set($val)
	{
		$this->result = $val;
		return $this;
	}

	/**
	 * Returns the object from
	 *
	 * @return mixed the object
	 * @throws \OutOfBoundsException if there's no object
	 */
	public function getObject()
	{
		if ($this->object === null)
		{
			throw new \OutOfBoundsException;
		}

		return $this->object;
	}

	/**
	 * Returns the array of parameters
	 *
	 * @param bool $orig whether we want the original array of parameters
	 * @return array
	 */
	public function getParams($orig = false)
	{
		if ($orig === true)
		{
			return $this->params_original;
		}

		return $this->params;
	}

	/**
	 * Returns the parameter with the key
	 *
	 * @param string $key
	 * @param bool $orig whether we want the original value of the parameter
	 * @return mixed
	 * @throws \OutOfBoundsException if the key is not set
	 */
	public function getParam($key, $orig = false)
	{
		if ($orig === true)
		{
			if ( ! isset($this->params_original[$key]))
			{
				throw new \OutOfBoundsException;
			}

			return $this->params_original[$key];
		}

		if ( ! isset($this->params_original[$key]))
		{
			throw new \OutOfBoundsException;
		}

		return $this->params[$key];
	}

	/**
	 * Updates a parameter
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return \Foolz\Plugin\Result
	 */
	public function setParam($key, $value)
	{
		$this->params[$key] = $value;
		return $this;
	}
}