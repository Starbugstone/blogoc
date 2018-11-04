<?php

namespace App\Controllers\Ajax;

use App\Models\UserModel;
use Core\AjaxController;
use Core\Container;
use Core\JsonException;
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
            throw new JsonException("invalid call");
        }
        $email = $this->request->getData("email");
        try {
            $return = !$this->userModel->isEmailUsed($email);
        } catch (\Exception $e) {
            throw new JsonException($e->getMessage());
        }
        echo json_encode($return);

    }

    /**
     * @throws JsonException
     */
    public function toggleActivation()
    {
        $this->onlyAdmin();
        $this->onlyPost();
        $state = (bool)($this->request->getData("state") === 'true');
        $userId = (int)$this->request->getData("userId");

        $result = array();
        $result["success"] = false;
        $result["state"] = $state;
        $result["userId"] = $userId;

        // we can not update the Original Admin activation state
        if($userId !== 1)
        {
            $result["success"] = $this->userModel->activateUser(!$state, $userId);
            $result["state"] = !$state;
            $result["userId"] = $userId;
        }

        echo json_encode($result);
    }
}