<?php
namespace App\Controllers;

use \Core\View;


class Home extends \Core\Controller{

    public function __construct(){
        $Includes = new \App\Models\Includes();
        $this->data['navigation'] = $Includes->getMenu();
        $this->data['jumbotron'] = $Includes->getJumbotron();
    }

    public function index(){

        View::renderTemplate('Home.twig', $this->data);
    }
}