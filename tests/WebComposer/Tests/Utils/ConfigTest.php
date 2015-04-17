<?php
namespace WebComposer\Tests\Utils;

use WebComposer\BaseTest;
use WebComposer\Utils\Config;

class ConfigTest extends BaseTest{
    protected static $app;

    public static function setUpBeforeClass()
    {
        self::$app = self::createApplication();
        self::$app['config'] = new Config(self::$app, BASEPATH.'/app');
    }

    public function testLoadFile()
    {
        self::$app['config']->loadFile('app.php');
        $this->assertEquals(true, isset(self::$app['appbundle.config']));
    }

    /**
     * @depends testLoadFile
     */
    public function testGetItem()
    {
        $debugValue = self::$app['config']->getItem('debug');
        $this->assertEquals(true, $debugValue);
    }
}