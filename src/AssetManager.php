<?php

namespace Foolz\Plugin;

class AssetManager extends \Foolz\Package\AssetManager
{
    /**
     * Returns the Package object that created this instance of AssetManager
     *
     * @return  \Foolz\Theme\Theme|null  The Plugin object that created this instance of AssetManager
     */
    public function getPlugin()
    {
        return parent::getPackage();
    }
}
