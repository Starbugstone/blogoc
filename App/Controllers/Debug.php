<?php
namespace App\Controllers;
use \Core\View;


class Debug{
    use \App\Traits\Navigation;
    public function index($test = ''){
        echo 'Ok '.$test.'<br>';
        echo 'the router is working \\o/';
    }
    public function test($test = '', $test2 = ''){
        echo 'Ok '.$test.' - '.$test2.'<br>';
        echo 'the router is working \\o/';
    }

    public function testNav(){
        $nav = new \App\Controllers\Template\Navigation();
        echo $nav->index();
    }

    public function testNav2(){

        $data = \App\Traits\Navigation::getMenu();
        echo View::returnTemplate('Template/Navigation.twig', $data);
    }
}