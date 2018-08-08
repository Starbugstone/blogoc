<?php
namespace App\Controllers\Admin;

class Debug{
    public function index($test = ''){
        echo 'Ok '.$test.'<br>';
        echo 'the router is working \\o/';
    }
    public function test($test = '', $test2 = ''){
        echo 'Ok '.$test.' - '.$test2.'<br>';
        echo 'the router is working \\o/';
    }
    public function addTest($test = ''){
        echo 'Ok '.$test.'<br>';
        echo 'the router is working \\o/';
    }

}