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
        } else {
            $this->alertbox->setAlert("Not Admin", 'gfd');
            $this->alertbox->setAlert("TESTING", 'error');
            header("location: /"); //this to be put in the response class
            die();
            $this->data['userLevel'] = 'Visitor';
        }




        $this->renderView('Admin/Home');
    }
}