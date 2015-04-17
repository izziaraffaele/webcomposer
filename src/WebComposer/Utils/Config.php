<?php

namespace WebComposer\Utils;

use Silex\Application;
use Igorw\Silex\ConfigServiceProvider as BaseConfigServiceProvider;

class Config{

    private $app;
    private $prefix = 'appbundle.config';

    public function __construct(Application $app, $basePath = '', $environment = 'development')
    {
        $this->app =& $app;
        $this->basePath = rtrim($basePath);
        $this->environment = $environment;
    }

    public function loadFile($filename)
    {
        if(file_exists($this->basePath.'/config/'.$this->environment.'/'.$filename))
        {
            $filepath = $this->basePath.'/config/'.$this->environment.'/'.$filename;
        }
        elseif(file_exists($this->basePath.'/config/'.$filename))
        {
            $filepath = $this->basePath.'/config/'.$filename;
        }
        else
        {
            $this->app->abort(500, "Unable to find config file $filename");
        }

        $this->app->register(new BaseConfigServiceProvider($filepath,[],null,$this->prefix));
    }

    public function getItem($item)
    {
        return ( isset($this->app[$this->prefix][$item]) ) ? $this->app[$this->prefix][$item] : null;
    }
}