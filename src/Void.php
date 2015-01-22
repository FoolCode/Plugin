<?php

namespace Foolz\Plugin;

/**
 * Class to pass instead of primitive types
 *
 * @author Foolz <support@foolz.us>
 * @package Foolz\Plugin
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 */
class Void
{
    /**
     * Use to set default parameters to void
     *
     * @return  \Foolz\Plugin\Void
     */
    public static function forge()
    {
        return new static();
    }
}
