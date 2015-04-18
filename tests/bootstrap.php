<?php

define( 'ENVIRONMENT', 'test' );
define( 'BASEPATH', dirname(__DIR__) );

/**
 * Simple test harness for the time being, will delete it when I TDD out the 
 * massive spike that was the development of this provider :)
 */
$loader = require __DIR__."/../vendor/autoload.php";
$loader->add('WebComposer\Tests', __DIR__);