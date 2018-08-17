<?php
namespace App\Controllers\Ajax;

//to call, require /ajax/debug/'action'
use Core\AjaxController;

class Debug extends AjaxController { //could probably extend a core ajax controller
    public function test()
    {
        //$request = $this->container->getRequest();

        //echo '<p> request method : '.$_SERVER['REQUEST_METHOD'].'</p>';
        //header('Content-Type: application/json');

        if (isset($_SERVER['HTTP_REFERER'])) {
            echo '<h1>' . $_SERVER['HTTP_REFERER'] . '</h1>';
        }
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            echo '<h1>' . $_SERVER['HTTP_ORIGIN'] . '</h1>';
        }
        echo '<h1>Test Ok</h1>';
    }
}