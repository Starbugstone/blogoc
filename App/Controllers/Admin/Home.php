<?php

namespace App\Controllers\Admin;



class Home extends \Core\AdminController
{
    public function index()
    {
        //testing auth
        if ($this->auth->isAdmin()) {
            $this->data['userRole'] = 'Admin';
            $this->data['userLevel'] = $this->auth->getUserLevel();
        } elseif ($this->auth->isUser()) {
            $this->data['userRole'] = 'User';
            $this->data['userLevel'] = $this->auth->getUserLevel();
        }else {
            $this->alertBox->setAlert("You must be connected to acces the admin interface", 'warning');
            $this->container->getResponse()->redirect();
        }

        $this->renderView('Admin/Home');

    }
}