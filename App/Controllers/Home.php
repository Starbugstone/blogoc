<?php
namespace App\Controllers;

use \Core\View;


class Home extends \Core\Controller{

    //can I set this in the core extendable controller class ??
    //-----------------------------------------------
    use \App\Traits\Navigation;
    public function __construct(){
        $this->data = $this->getMenu();
    }
    //-----------------------------------------------

    public function index(){

        View::renderTemplate('Home.twig', $this->data);
    }
}