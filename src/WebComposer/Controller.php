<?php

namespace WebComposer;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    protected $twig;

    public function __construct(Application $app)
    {
        $this->twig = $app['twig'];
    }
}