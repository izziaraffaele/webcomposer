<?php
namespace WebComposer\Tests\Provider;

use WebComposer\Tests\TestCase;
use WebComposer\Provider\ConfigServiceProvider;

class ConfigServiceProviderTest extends TestCase{
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
        $this->assertEquals(1, count($allItems));
        $this->assertFalse($allItems['app']['is_testing']);
    }
    /**
     * @depends testLoadFile
     */
    public function testLoadFiles()
    {
        $this->app['config.path'] = BASEPATH.'/app';
        $this->app->register(new ConfigServiceProvider());
        $this->app['config']->loadFiles([
            'app.php',
            'test/replacements.json'
        ]);
        $allItems = $this->app['config']->getAllItems();
        $this->assertEquals(2, count($allItems));
    }
    /**
     * @depends testLoadFiles
     */
    public function testAutoloadFiles()
    {
        $this->app['config.path'] = BASEPATH.'/app';
        $this->app['config.autoload'] = [
            'app.php',
            'test/replacements.json'
        ];

        $this->app->register(new ConfigServiceProvider());
        $allItems = $this->app['config']->getAllItems();
        $this->assertEquals(2, count($allItems));
    }
    /**
     * @depends testLoadFile
     */
    public function testLoadEnvironmentFile()
    {
        $this->app['config.path'] = BASEPATH.'/app';
        $this->app['config.environment'] = 'test';
        $this->app->register(new ConfigServiceProvider());
        
        $this->app['config']->loadFile('app.php');
        $allItems = $this->app['config']->getAllItems();

        $this->assertGreaterThanOrEqual(1, count($allItems));
        $this->assertTrue($allItems['app']['is_testing']);
    }
    /**
     * @depends testLoadFile
     */
    public function testGetItem()
    {
        $this->app['config.path'] = BASEPATH.'/app';
        $this->app->register(new ConfigServiceProvider());

        $this->app['config']->loadFile('app.php');
        $this->assertTrue($this->app['config']->getItem('app')['debug']);
    }
    /**
     * @depends testLoadFile
     */
    public function testReplacements()
    {
        $this->app['config.path'] = BASEPATH.'/app';
        $this->app['config.environment'] = 'test';
        $this->app->register(new ConfigServiceProvider());

        $replaced_value = 'I was replaced';
        $this->app['config']->setGlobalReplacements([ 'replace_me' => $replaced_value]);
        $this->app['config']->loadFile('replacements.json');
        $this->assertContains($replaced_value, $this->app['config']->getItem('replacements')['to_replace'], 'Key was not replaced correctly', true);
    }
}