<?php

namespace WebComposer;

use Silex\Application;

class BaseTest extends \PHPUnit_Framework_TestCase{

    public static function createApplication()
    {
        $app = new Application;
        $app['debug'] = true;

        return $app;
    }
}