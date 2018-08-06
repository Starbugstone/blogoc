<?php
namespace App\Controllers;
use \Core\View;
use \App\Traits\Navigation;

class Debug{

    public function index($test = ''){
        echo 'Ok '.$test.'<br>';
        echo 'the router is working \\o/';
    }
    public function test($test = '', $test2 = ''){
        echo 'Ok '.$test.' - '.$test2.'<br>';
        echo 'the router is working \\o/';
    }


    public function testNav(){

        $data = Navigation::getMenu();
        echo View::returnTemplate('Traits/Navigation.twig', $data);
    }
}