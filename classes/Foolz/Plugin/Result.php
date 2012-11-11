<?php

namespace Foolz\Plugin;

/**
 * The result that is cascaded through events on the same key and returned by the hook
 *
 * @author Foolz <support@foolz.us>
 * @package Foolz\Plugin
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 */
class Result
{
	/**
	 * The parameters (which can be modified with setParam())
	 *
	 * @var  array  Array with as keys the parameter key
	 */
	protected $params = [];

	/**
	 * The original parameters (can't be modified)
	 *
	 * @var  array  Array with as key the parameter key
	 */
	protected $params_original = [];

	/**
	 * The object in which the Hook runs
	 *
	 * @var  object
	 */
	protected $object = null;

	/**
	 * The result
	 *
	 * @var  mixed
	 */
	protected $result = null;

	/**
	 * Sets the parameters and in case it's available the object
	 *
	 * @param  array        $params
	 * @param  null|object  $object
	 */
	public function __construct(array $params = array(), $object = null)
	{
		$this->params = $this->params_original = $params;
		$this->object = $object;
		$this->result = new Void();
	}

	/**
	 * Resets the object to the initial state
	 *
	 * @return  \Foolz\Plugin\Result  The current object
	 */
	public function reset()
	{
		$this->params = [];
		$this->params_original = [];
		$this->object = null;
		$this->result = null;
		return $this;
	}

	/**
	 * Returns the result
	 *
	 * @return  mixed
	 */
	public function get($fallback = null)
	{
		if ($this->result instanceof Void && func_num_args() === 1)
		{
			return $fallback;
		}

		return $this->result;
	}

	/**
	 * Sets the result
	 *
	 * @param   mixed  $val
	 * @return  \Foolz\Plugin\Result
	 */
	public function set($val)
	{
		$this->result = $val;
		return $this;
	}

	/**
	 * Returns the object from
	 *
	 * @return   mixed  the object
	 * @throws   \OutOfBoundsException if there's no object
	 */
	public function getObject()
	{
		if ($this->object === null)
		{
			throw new \OutOfBoundsException('No object has been set.');
		}

		return $this->object;
	}

	/**
	 * Returns the array of parameters
	 *
	 * @param   bool  $orig  whether we want the original array of parameters
	 * @return  array
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
	 * @param   string  $key
	 * @param   bool    $orig whether we want the original value of the parameter
	 * @return  mixed
	 * @throws  \OutOfBoundsException if the key is not set
	 */
	public function getParam($key, $orig = false)
	{
		if ($orig === true)
		{
			if ( ! isset($this->params_original[$key]))
			{
				throw new \OutOfBoundsException('Undefined original parameter.');
			}

			return $this->params_original[$key];
		}

		if ( ! isset($this->params[$key]))
		{
			throw new \OutOfBoundsException('Undefined parameter.');
		}

		return $this->params[$key];
	}

	/**
	 * Updates a parameter
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  \Foolz\Plugin\Result
	 */
	public function setParam($key, $value)
	{
		$this->params[$key] = $value;
		return $this;
	}

	/**
	 * Updates several parameters
	 *
	 * @param   array  $array  Array with as keys the parameter key and as value the parameter value
	 * @return  \Foolz\Plugin\Result
	 */
	public function setParams($array)
	{
		foreach ($array as $key => $item)
		{
			$this->params[$key] = $item;
		}
		return $this;
	}
}