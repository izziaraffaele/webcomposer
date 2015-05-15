<?php
namespace WebComposer\Tests;

use WebComposer\Tests\TestCase;
use WebComposer\Application;

class ApplicationTest extends TestCase{
    protected static $app;

    public static function setUpBeforeClass()
    {
        self::$app = new Application([
            'debug' => true,
            'config.path' => APPPATH,
            'config.environment' => 'development',
            'config.replacements' =>[
                'base_path' => BASEPATH
            ]
        ]);
    }
    public function testConstructorInitConfig()
    {
        $this->arrayHasKey(self::$app, 'config');
    }
    public function testInitCache()
    {
        self::$app->initCache();
        $this->arrayHasKey(self::$app, 'cache.path');
    }
    public function testInitTemplate()
    {
        self::$app->initTemplate();
        $this->arrayHasKey(self::$app, 'twig');
    }
}