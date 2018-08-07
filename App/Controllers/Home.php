<?php
namespace App\Controllers;

use \Core\View;


class Home extends \Core\Controller{

    //can I set this in the core extendable controller class ??
    //-----------------------------------------------
    //use \App\Traits\Navigation;
    public function __construct(){
        $menu = new \App\Models\Menu();

        $this->data = $menu->getMenu();
    }
    //-----------------------------------------------

    public function index(){

        View::renderTemplate('Home.twig', $this->data);
    }
}