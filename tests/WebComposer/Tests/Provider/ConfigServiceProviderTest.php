<?php
namespace WebComposer\Tests\Provider;

use WebComposer\BaseTest;
use WebComposer\Provider\ConfigServiceProvider;

class ConfigServiceProviderTest extends BaseTest{
    protected $app;

    public function setup()
    {
        $this->app = $this->createApplication();
    }

    public function testLoadFile()
    {
        $this->app['config.path'] = BASEPATH.'/app';
        $this->app->register(new ConfigServiceProvider());

        $this->app['config']->loadFile('app.php');
        $allItems = $this->app['config']->getAllItems();
        $this->assertGreaterThanOrEqual(1, count($allItems));
        $this->assertFalse($allItems['is_testing']);
    }

    public function testLoadEnvironmentFile()
    {
        $this->app['config.path'] = BASEPATH.'/app';
        $this->app['environment'] = 'test';
        $this->app->register(new ConfigServiceProvider());
        
        $this->app['config']->loadFile('app.php');
        $allItems = $this->app['config']->getAllItems();

        $this->assertGreaterThanOrEqual(1, count($allItems));
        $this->assertTrue($allItems['is_testing']);
    }

    /**
     * @depends testLoadFile
     */
    public function testGetItem()
    {
        $this->app['config.path'] = BASEPATH.'/app';
        $this->app->register(new ConfigServiceProvider());

        $this->app['config']->loadFile('app.php');
        $this->assertTrue($this->app['config']->getItem('debug'));
    }
}