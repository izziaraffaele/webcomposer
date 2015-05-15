<?php

namespace WebComposer;

use Silex\Application as Container;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Whoops\Provider\Silex\WhoopsServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use MJanssen\Provider\ServiceRegisterProvider;
use MJanssen\Provider\RoutingServiceProvider;
use Silex\provider\TwigServiceProvider;

class Application extends Container{
    /**
     * Initialize config service provider
     * @return void
     */
    public function initConfig()
    {
        $this->register(new Provider\ConfigServiceProvider());
    }
    /**
     * Initialize error handlers
     * @return void
     */
    public function initErrorHandler()
    {
        ErrorHandler::register();
        if ('cli' !== php_sapi_name()) 
        {
            ExceptionHandler::register();
        }
        // PHP 5.3 does not allow 'use ($this)' in closures.
        $app = $this;
        $this->error(function (\Exception $exception, $code) use ($app)
        {
            if ($app['debug'] && $code !== 404) 
            {
                return;
            }
            
            // 404.html, or 40x.html, or 4xx.html, or error.html
            $templates = array(
                '@app/errors/'.$code.'.html.twig',
                '@app/errors/'.substr($code, 0, 2).'x.html.twig',
                '@app/errors/'.substr($code, 0, 1).'xx.html.twig',
                '@app/errors/'.'default.html.twig',
            );

            return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
        });
        if( $this['debug'] )
        {
            // manually register whoops error handler
            $this->register(new WhoopsServiceProvider(),[
                'whoops.error_page_handler' => 'sublime'
            ]);
        }
    }
    /**
     * Initialize template engine
     * @return void
     */
    public function initTemplate()
    {
        $this->register(new TwigServiceProvider());
        $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
            $twig->addPath(APPPATH.'/app/templates','app');
            $twig->addPath(APPPATH.'/app/views','app');

            $appConfig = $this['config']->getItem('app');
            if($appConfig['path.views'])
            {
                foreach ($appConfig['path.views'] as $path) 
                {
                    $twig->addPath($path,'app');
                }
            }

            return $twig;
        }));
    }
    public function initProviders()
    {
        $serviceRegisterProvider = new ServiceRegisterProvider();
        $serviceRegisterProvider->registerServiceProviders($this, $this['config']->getItem('providers'));
    }
    public function initControllers()
    {
        $controllers = $this['config']->getItem('controllers');
        if(isset($controllers))
        {
            foreach ( $controllers as $name => $controllerClass) 
            {
                $app['controller.'.$name] = $app->share(function() use ( $controllerClass ) {
                    return new $controllerClass();
                });
            }
        }
    }
    public function initRoutes()
    {
        $router = new RoutingServiceProvider();
        $router->addRoutes($app, $app['config']->getItem('routes'));
    }
    public function initCache()
    {
        $appConfig = $this['config']->getItem('app')
        $cachePath = $appConfig['cache_path'] ?: BASEPATH.'/storage';
        $root = $cachePath.'/'.ENVIRONMENT;

        $this['twig.options'] = ['cache' => $root.'/twig'];
    }
}