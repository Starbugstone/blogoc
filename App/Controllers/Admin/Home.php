<?php

namespace App\Controllers\Admin;



class Home extends \Core\AdminController
{
    public function index()
    {
        if(!$this->auth->isUser()){
            $this->alertBox->setAlert("You must be connected to access the admin interface", 'warning');
            $this->container->getResponse()->redirect();
        }

        $this->renderView('Admin/Home');

    }
}