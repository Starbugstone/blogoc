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
        if(!$this->auth->isUser()){
            $this->alertBox->setAlert("You must be connected to access the admin interface", 'warning');
            $this->container->getResponse()->redirect();
        }

        $this->renderView('Admin/Home');

    }
}