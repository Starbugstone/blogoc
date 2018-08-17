<?php
namespace App\Controllers\Ajax;

//to call, require /ajax/debug/'action'
use Core\AjaxController;

class Debug extends AjaxController { //could probably extend a core ajax controller
    public function test()
    {


        $result = $this->jsonResponse('Working');
        echo $result;


    }
}