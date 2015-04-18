<?php

namespace WebComposer\Utils;

use Silex\Application;
use Igorw\Silex\ChainConfigDriver;
use Igorw\Silex\ConfigDriver;
use Igorw\Silex\PhpConfigDriver;
use Igorw\Silex\YamlConfigDriver;
use Igorw\Silex\JsonConfigDriver;
use Igorw\Silex\TomlConfigDriver;

class Config{

    private $basePath;
    private $environment;
    private $replacements = [];
    private $_config = [];

    public function __construct($path = '', $environment = 'development')
    {
        $this->basePath = rtrim($path);
        $this->environment = $environment;
    }

    public function loadFile($filename)
    {
        $config = $this->readConfig($filename);

        foreach ($config as $name => $value)
            if ('%' === substr($name, 0, 1))
                $this->replacements[$name] = (string) $value;

        $this->merge($config);
    }

    public function getItem($item)
    {
        return ( isset($this->_config[$item]) ) ? $this->_config[$item] : null;
    }

    public function getAllItems()
    {
        return $this->_config;
    }

    private function readConfig($filename, ConfigDriver $driver = null)
    {
        if (!$filename) {
            throw new \RuntimeException('A valid configuration file must be passed before reading the config.');
        }

        if(file_exists($this->basePath.'/config/'.$this->environment.'/'.$filename))
        {
            $filepath = $this->basePath.'/config/'.$this->environment.'/'.$filename;
        }
        elseif(file_exists($this->basePath.'/config/'.$filename))
        {
            $filepath = $this->basePath.'/config/'.$filename;
        }

        if (!isset($filepath)) {
            throw new \InvalidArgumentException(
                sprintf("The config file '%s' does not exist.", $filename));
        }

        $driver = $driver ?: new ChainConfigDriver(array(
            new PhpConfigDriver(),
            new YamlConfigDriver(),
            new JsonConfigDriver(),
            new TomlConfigDriver(),
        ));

        if ($driver->supports($filepath)) {
            return $driver->load($filepath);
        }

        throw new \InvalidArgumentException(
                sprintf("The config file '%s' appears to have an invalid format.", $filename));
    }

    private function merge(array $config)
    {
        foreach ($config as $name => $value) {
            if (isset($this->_config[$name]) && is_array($value)) {
                $this->_config[$name] = $this->mergeRecursively($this->_config[$name], $value);
            } else {
                $this->_config[$name] = $this->doReplacements($value);
            }
        }
    }

    private function mergeRecursively(array $currentValue, array $newValue)
    {
        foreach ($newValue as $name => $value) {
            if (is_array($value) && isset($currentValue[$name])) {
                $currentValue[$name] = $this->mergeRecursively($currentValue[$name], $value);
            } else {
                $currentValue[$name] = $this->doReplacements($value);
            }
        }

        return $currentValue;
    }

    private function doReplacements($value)
    {
        if (!$this->replacements) {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->doReplacements($v);
            }

            return $value;
        }

        if (is_string($value)) {
            return strtr($value, $this->replacements);
        }

        return $value;
    }
}