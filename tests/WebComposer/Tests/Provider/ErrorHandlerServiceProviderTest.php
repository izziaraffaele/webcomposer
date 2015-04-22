<?php
namespace WebComposer\Tests\Provider;

use WebComposer\Tests\TestCase;
use WebComposer\Provider\ErrorHandlerServiceProvider;

class ErrorHandlerServiceProviderTest extends TestCase{
    protected $app;

    public function setup()
    {
        $this->app = $this->createApplication();
    }

    public function testRegister()
    {
        $this->app['debug'] = true;
        $error = FALSE;
        try
        {
            $this->app->register(new ErrorHandlerServiceProvider());
        }
        catch(\Exception $e)
        {
            $error = TRUE;
        }

        $this->assertFalse($error);
    }
}