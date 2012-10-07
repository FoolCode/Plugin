<?php

namespace Foolz\Plugin;

// class to pass instead of primitive types
class Void
{

	/**
	 * Use to set default parameters to void
	 *
	 * @return \Foolz\Plugin\Void
	 */
	public static function forge()
	{
		return new static();
	}

}