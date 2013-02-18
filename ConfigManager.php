<?php

namespace Undf\AngularJsBundle;

use Assetic\Filter\FilterInterface;
use Assetic\Asset\AssetInterface;

/**
 * Config Manager
 *
 * @author Dani Gonzalez <daniel.gonzalez@undefined.es>
 */
class ConfigManager
{

    private $configs;

    public function __construct(array $configs)
    {
        $this->configs = $configs;
    }

    public function getConfigs()
    {
        return $this->configs;
    }

    public function getConfig($name)
    {
        if (!isset($this->configs[$name])) {
            throw new \Exception(sprintf("Config '%s' doesn´t exist.", $name));
        }
        return $this->configs[$name];
    }

    public function getConfigNameFromMasterFile($filename)
    {
        $filename = trim(strrchr($filename, '/'),' /');
        $parts = explode('.', $filename);
        if(($num = count($parts)) > 2 && $parts[$num - 2] == 'undfangular') {
            return $parts[$num - 3];
        }
        return false;
    }

    public function getMasterFileForConfig($configname)
    {
        return $configname.'.undfangular.js';
    }

}
