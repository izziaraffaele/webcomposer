<?php

namespace WebComposer\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use WebComposer\Utils\Config;

class ConfigServiceProvider implements ServiceProviderInterface{

    public function register(Application $app)
    {
        $environment = (isset($app['environment'])) ? $app['environment'] : 'development';
        $app['config'] = new Config($app['config.path'],$environment);
    }

    public function boot(Application $app)
    {
    }
}
