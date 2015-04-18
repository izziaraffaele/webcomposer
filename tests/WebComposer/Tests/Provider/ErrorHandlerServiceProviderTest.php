<?php
namespace WebComposer\Tests\Provider;

use WebComposer\BaseTest;
use WebComposer\Provider\ErrorHandlerServiceProvider;

class ErrorHandlerServiceProviderTest extends BaseTest{
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