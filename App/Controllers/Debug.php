<?php
namespace App\Controllers;
use \Core\View;
use \App\Traits\Navigation;

class Debug extends \Core\Controller{

    public function __construct(){
        $Includes = new \App\Models\Includes();

        $this->data = $Includes->getMenu();
    }

    public function index($test = ''){
        echo 'Ok '.$test.'<br>';
        echo 'the router is working \\o/';
    }
    public function test($test = '', $test2 = ''){
        echo 'Ok '.$test.' - '.$test2.'<br>';
        echo 'the router is working \\o/';
    }


    public function testNav(){

        echo View::returnTemplate('Includes.php/Menu.twig', $this->data);
    }
}