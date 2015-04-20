<?php

namespace WebComposer;

use Symfony\Component\HttpFoundation\Response;

class Controller
{
    protected $twig;

    public function __construct($app)
    {
        $this->twig = $app['twig'];
    }
}