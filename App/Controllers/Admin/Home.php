<?php

namespace App\Controllers\Admin;



class Home extends \Core\AdminController
{
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