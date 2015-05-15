<?php

namespace WebComposer\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use WebComposer\Component\Core\Config;

class ConfigServiceProvider implements ServiceProviderInterface{
    const DEFAULT_ENVIRONMENT = 'development';

    public function register(Application $app)
    {
        if(!isset($app['config.environment']))
        {
            $app['config.environment'] = self::DEFAULT_ENVIRONMENT;
        }

        $app['config'] = new Config($app['config.path'],$app['config.environment']);

        if(isset($app['config.replacements']))
        {
            $app['config']->setGlobalReplacements($app['config.replacements']);
        }

        if(isset($app['config.autoload']))
        {
            $app['config']->loadFiles($app['config.autoload']);
        }
    }

    public function boot(Application $app)
    {
    }
}
