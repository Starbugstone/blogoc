<?php
namespace App\Controllers\Ajax;

//to call, require /ajax/debug/'action'
class Debug{ //could probably extend a core ajax controller
    public function test(){
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&       strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            echo '<h1>Test Ok</h1>';
        }else{
            throw new \Exception('Call not permitted');
        }

    }
}