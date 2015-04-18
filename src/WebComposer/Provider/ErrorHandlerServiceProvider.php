<?php

namespace WebComposer\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ErrorHandlerServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        ErrorHandler::register();
        ExceptionHandler::register($app['debug']);

        $app->error(function (\Exception $exception, $code) use ($app)
        {
            if (!$app['debug'] || $code === 404) 
            {
                // 404.html, or 40x.html, or 4xx.html, or error.html
                $templates = array(
                    $code.'.html.twig',
                    substr($code, 0, 2).'x.html.twig',
                    substr($code, 0, 1).'xx.html.twig',
                    'default.html.twig',
                );

                return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
            }
        });
    }

    public function boot(Application $app)
    {
    }
}