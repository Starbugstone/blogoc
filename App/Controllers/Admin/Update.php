<?php
namespace App\Controllers\Admin;

use Core\AdminController;
use Core\Container;

class Update extends AdminController {


    public function index(){
        echo'TEST';
    }

    public function updateConfig(){
        //$this->onlyAdmin();
        var_dump($_SESSION);
        die();
    }
}