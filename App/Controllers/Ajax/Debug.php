<?php
namespace App\Controllers\Ajax;

//to call, require /ajax/debug/'action'
use Core\AjaxController;

class Debug extends AjaxController { //could probably extend a core ajax controller
    public function test()
    {
        $referer = $this->request->getReferer();
        $host = $this->request->getBaseUrl();

        $jsonData = [
            "Referer" =>   $referer,
            "https" => $host,
            "Message" => 'Ok message sent'
        ];
        $result = $this->jsonResponse($jsonData);
        echo $result;



    }
}