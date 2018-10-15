<?php

namespace App\Controllers\Admin;



use Core\Container;

class Home extends \Core\AdminController
{
    protected $siteConfig;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        parent::__construct($container);
    }

    /**
     * The front page of the admin section
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index()
    {
        $this->onlyUser();

        //need to send the session info for user / admin rights

        $this->renderView('Admin/Home');

    }
}