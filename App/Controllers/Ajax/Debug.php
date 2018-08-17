<?php
namespace App\Controllers\Ajax;

//to call, require /ajax/debug/'action'
class Debug{ //could probably extend a core ajax controller
    public function test(){

        header('Content-Type: application/json');
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&       strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            if(isset($_SERVER['HTTP_REFERER'])){
                echo '<h1>'.$_SERVER['HTTP_REFERER'].'</h1>';
            }
            if (isset($_SERVER['HTTP_ORIGIN'])){
                echo '<h1>'.$_SERVER['HTTP_ORIGIN'].'</h1>';
            }
            echo '<h1>Test Ok</h1>';
        }else{
            throw new \Exception('Call not permitted');
        }
    }
}