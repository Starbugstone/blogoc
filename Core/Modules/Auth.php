<?php

namespace Core\Modules;

use Core\Constant;
use \Core\Container;

/**
 * Authentication class taking care of access rights
 * Class Auth
 * @package Core\Modules
 */
class Auth extends Module
{

    /**
     * get the user type
     * @return mixed
     */
    public function getUser()
    {
        $session = $this->container->getSession();
        return $session->get('user_role_name');
    }

    /**
     * Gets the user level defined in the session (this is set on login and also stored in the DB).
     * Returns an int for easier user control.
     * @return int
     */
    public function getUserLevel(): int
    {
        $session = $this->container->getSession();
        return $session->get('user_role_level') ?? 0;
    }

    /**
     * Gets the user role name
     * @return string
     */
    public function getUserRole():string
    {
        $session = $this->container->getSession();
        return $session->get('user_role_name') ?? '';
    }

    /**
     * gets the configured levels defined in the constant file
     * @return \stdClass
     */
    public function getLevelConst():\stdClass
    {
        $levels = new \stdClass();
        $levels->userLevel = Constant::USER_LEVEL;
        $levels->adminLevel = Constant::ADMIN_LEVEL;

        return $levels;
    }

    /**
     * is the connected user an Admin
     * @return bool
     */
    public function isAdmin():bool
    {
        $userLevel = $this->getUserLevel();
        if ($userLevel >= Constant::ADMIN_LEVEL) {
            return true;
        }
        return false;
    }

    /**
     * is the user connected ?
     * @return bool
     */
    public function isUser():bool
    {
        $userLevel = $this->getUserLevel();
        if ($userLevel >= Constant::USER_LEVEL) {
            return true;
        }
        return false;
    }
}