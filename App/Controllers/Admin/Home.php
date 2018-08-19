<?php
namespace App\Controllers\Admin;


class Home extends \Core\AdminController {
    public function index(){

        $this->data['userLevel'] = $this->getUserLevel();

        $this->renderView('Admin/Home');
    }
}