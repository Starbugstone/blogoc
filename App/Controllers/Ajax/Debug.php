<?php
namespace App\Controllers\Ajax;

//to call, require /ajax/debug/'action'
use Core\AjaxController;

class Debug extends AjaxController { //could probably extend a core ajax controller
    public function test()
    {

        $jsonData = [
            "Referer" =>   $_SERVER['HTTP_REFERER'], //This can be altered and even some navigators won't send it. Not relying on it to secure the ajax call. Could stil do a check
            "Host" => $_SERVER['HTTP_HOST'],
            "https" => !empty($_SERVER['HTTPS']) ? 'https' : 'http', //concatenate https and host and match to the referer start should get a bit more security.
            "Message" => 'Ok message sent'
        ];
        $result = $this->jsonResponse($jsonData);
        echo $result;



    }
}