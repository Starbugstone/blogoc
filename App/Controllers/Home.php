<?php
namespace App\Controllers;

use \Core\View;

class Home{
    public function index(){
        View::renderTemplate('Home.twig');
    }
}