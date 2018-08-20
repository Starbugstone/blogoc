<?php

namespace App\Controllers\Admin;


class Home extends \Core\AdminController
{
    public function index()
    {

        if ($this->auth->isAdmin()) {
            $this->data['userLevel'] = 'Admin';
        } elseif ($this->auth->isUser()) {
            $this->data['userLevel'] = 'User';
        } else {
            $this->data['userLevel'] = 'Visitor';
        }


        $this->renderView('Admin/Home');
    }
}