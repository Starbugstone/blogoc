<?php
namespace App\Controllers\Admin;

use \Core\View;

class Home{
    public function index(){
        View::renderTemplate('Admin/Home.twig');
    }
}