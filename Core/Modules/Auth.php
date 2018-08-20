<?php

namespace Core\Modules;

use \Core\Container;

/**
 * Authentication class taking care of access rights
 * Class Auth
 * @package Core\Modules
 */
class Auth extends Module
{

    /**
     * Gets the user level defined in the session (this is set on login and also stored in the DB).
     * Returns an int for easier user control.
     * @return int
     */
    public function getUserLevel()
    {
        $session = $this->container->getSession();
        //For testing, setting the user level
        $session->set('session_level', 'bla');

        //get session level from the actual $_SESSION
        $sessionLevel = $session->get('session_level');
        //we could use a binary system for the rights but not much granular levels to take care of
        if ($sessionLevel) {
            if ($sessionLevel === 'Admin') {
                return 2;
            }
            if ($sessionLevel === 'User') {
                return 1;
            }
        }

        return 0;
    }

    /**
     * is the connected user an Admin
     * @return bool
     */
    public function isAdmin()
    {
        $userLevel = $this->getUserLevel();
        if ($userLevel > 1) {
            return true;
        }
        return false;
    }

    /**
     * is the user connected ?
     * @return bool
     */
    public function isUser()
    {
        $userLevel = $this->getUserLevel();
        if ($userLevel > 0) {
            return true;
        }
        return false;
    }
}