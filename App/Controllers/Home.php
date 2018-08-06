<?php
namespace App\Controllers;

use \Core\View;

class Home{
    public function index(){
        $data = \App\Traits\Navigation::getMenu();
        View::renderTemplate('Home.twig', $data);
    }
}