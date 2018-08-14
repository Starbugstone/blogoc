<?php
namespace App\Controllers\Admin;


class Home extends \Core\Controller {
    public function index(){
        $this->view->renderTemplate('Admin/Home.twig');
    }
}