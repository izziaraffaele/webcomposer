<?php

namespace WebComposer\Utils;

use Silex\Application;
use Igorw\Silex\ChainConfigDriver;
use Igorw\Silex\ConfigDriver;
use Igorw\Silex\PhpConfigDriver;
use Igorw\Silex\YamlConfigDriver;
use Igorw\Silex\JsonConfigDriver;
use Igorw\Silex\TomlConfigDriver;

/**
 * Config
 * Based on https://github.com/igorw/ConfigServiceProvider it can read multiple config file type
 * and stores data internally. You can than read all data or single items.
 *
 * @author Izzia Raffaele <izziaraffaele@gmail.com>
 * @package WebComposer
 * @subpackage Utils
 * @see https://github.com/igorw/ConfigServiceProvider
 * @copyright Copyright (c) 2015, Izzia Raffaele
 */
class Config{
    /**
     * Base path for config files
     * @var string
     */
    private $basePath;

    /**
     * Environment
     * @var string
     */
    private $environment;

    /**
     * Tags to replace in config files
     * @var array
     */
    private $replacements = [];

    /**
     * Contains current config items
     * @var arrat
     */
    private $_config = [];

    /**
     * __construct
     * 
     * @param string $path        Base path for config files
     * @param string $environment App environment
     */
    public function __construct($path = '', $environment = 'development')
    {
        $this->basePath = rtrim($path);
        $this->environment = $environment;
    }

    /**
     * Load specific config file
     * 
     * @param  string $filename Path to config file
     * @return void
     */
    public function loadFile($filename)
    {
        $config = $this->readConfig($filename);

        foreach ($config as $name => $value)
            if ('%' === substr($name, 0, 1))
                $this->replacements[$name] = (string) $value;

        $this->merge($config);
    }

    /**
     * Return single config item
     * 
     * @param  string $item Item slug to retrive
     * @return mixed
     */
    public function getItem($item)
    {
        return ( isset($this->_config[$item]) ) ? $this->_config[$item] : null;
    }

    /**
     * Return all config items
     * 
     * @return array
     */
    public function getAllItems()
    {
        return $this->_config;
    }

    /**
     * Read file
     *
     * @access private
     * @param  string            $filename File to read
     * @param  ConfigDriver|null $driver   Config driver
     * @return void
     */
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

    /**
     * Merges current configuration with new items
     *
     * @access private
     * @param  array  $config New items
     * @return void
     */
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

    /**
     * Merges recursively config items
     *
     * @access private
     * @param  array  $currentValue 
     * @param  array  $newValue 
     * @return void
     */
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

    /**
     * Replace tags in config files
     *
     * @access private
     * @param  mixed  $value  Values to replace 
     * @return mixed 
     */
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