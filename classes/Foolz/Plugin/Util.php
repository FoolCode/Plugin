<?php

namespace Foolz\Plugin;

/**
 * Collection of utilities used in Foolz\Plugin
 */
class Util
{

	public static function dottedConfig($config, $key, $fallback)
	{
		// get the section with the dot separated string
		$sections = explode('.', $section);
		$current = $config;
		foreach ($sections as $key)
		{
			if (isset($current[$key]))
			{
				$current = $current[$key];
			}
			else
			{
				if ($throw)
				{
					throw new \DomainException;
				}

				return $fallback;
			}
		}
	}

	/**
	 *
	 * @param type $array
	 * @return \Foolz\Plugin\Plugin
	 */
	public static function saveArrayToFile($path, $array)
	{
		$content = "<?php \n".
		"return ".var_export($array, true);

		file_put_contents($path, $content);
	}

}