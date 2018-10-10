<?php

namespace App\Controllers\Ajax;

use App\Models\UserModel;
use Core\AjaxController;
use Core\Container;
use Core\Traits\StringFunctions;

class User  extends AjaxController{

    use StringFunctions;

    private $userModel;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->userModel = new UserModel($this->container);
    }

    public function isEmailUnique($get)
    {
        //the router needs a parameter with get functions else throsw a wobbly
        //we pass a get variable and call the /controller/function/get?bla
        //for better use and security, we must pass "get" as the parameter
        if(!$this->startsWith(strtolower($get),"get"))
        {
            throw new \Exception("invalid call");
        }
        $email = $this->request->getData("email");
        $return =  !$this->userModel->isEmailUsed($email);
        echo json_encode($return);

    }
}