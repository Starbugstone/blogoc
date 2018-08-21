<?php

namespace App\Controllers\Admin;


class Home extends \Core\AdminController
{
    public function index()
    {
        //testing auth
        if ($this->auth->isAdmin()) {
            $this->data['userLevel'] = 'Admin';
        } elseif ($this->auth->isUser()) {
            $this->data['userLevel'] = 'User';
        }else {
            $this->alertBox->setAlert("Not Admin", 'warning');
            $this->alertBox->setAlert("TESTING", 'error');
            $this->container->getResponse()->redirect();
            $this->data['userLevel'] = 'Visitor';
        }

        $this->renderView('Admin/Home');
    }
}