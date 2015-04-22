<?php

namespace WebComposer;

use Silex\Application;

/**
 * TestCase
 * Base class for all unit testing suites
 *
 * @package WebComposer
 * @author Izzia Raffaele <izziaraffaele@gmail.com>
 * @copyright 2015 Izzia Raffaele
 */
class TestCase extends \PHPUnit_Framework_TestCase{
    /**
     * Create the application
     * 
     * @return Silex\Application 
     */
    public function createApplication()
    {
        $app = new Application;
        $app['debug'] = true;
        $app['exception_handler']->disable();
        return $app;
    }
}