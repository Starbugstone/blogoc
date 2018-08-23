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
        echo '<pre>';
        var_dump($_POST);
        var_dump($_SESSION);
        die();
    }
}