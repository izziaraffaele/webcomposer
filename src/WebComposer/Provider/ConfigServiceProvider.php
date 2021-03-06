<?php

namespace WebComposer\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use WebComposer\Component\Core\Config;

class ConfigServiceProvider implements ServiceProviderInterface{

    public function register(Application $app, $replacements = array())
    {
        $environment = (isset($app['environment'])) ? $app['environment'] : 'development';
        $app['config'] = new Config($app['config.path'],$environment);
    }

    public function boot(Application $app)
    {
    }
}
