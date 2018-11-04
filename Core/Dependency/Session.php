<?php

namespace Core\Dependency;

/**
 * Class Session
 * Session class to take care of all the session details
 *
 * @package Core
 *
 * PHP version 7
 *
 * this is our session handler, of course we access the session superglobal.
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class Session
{
    /**
     * Session constructor. it the session isn't started then we start it
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * get the session values
     *
     * @param  $param string  the name of the parameter to get
     * @return mixed
     */
    public function get($param)
    {

        return $_SESSION[$param] ?? null;
    }

    /**
     * Checks if a parameter is set in the session
     * @param $param
     * @return bool
     */
    public function isParamSet($param): bool
    {
        return isset($_SESSION[$param]);
    }

    /**
     * set the session parameter to somthing
     *
     * @param $param string  paramter to set
     * @param $info mixed  value to set the session param to
     */
    public function set($param, $info): void
    {
        $_SESSION[$param] = $info;
    }

    /**
     * Set the session parameter only if nothing has been set yet
     * @param $param string parameter to set
     * @param $info mixed the info to store
     */
    public function setOnce($param, $info): void
    {
        if (!isset($_SESSION[$param])) {
            $_SESSION[$param] = $info;
        }
    }

    /**
     * removes elements from the session
     * @param $param string parameter to remove
     */
    public function remove($param): void
    {
        unset($_SESSION[$param]);
    }

    /**
     * Remove all the session variables.
     */
    public function unsetAll(): void
    {
        session_unset(); //remove all session variables
        $this->regenerateSessionId(); //regenerate the ID
    }

    /**
     * return the entire session superglobal.
     * @return mixed
     */
    public function getAllSessionVars()
    {
        return $_SESSION;
    }

    /**
     * Unsets all the data the destroys the session
     */
    public function destroySession():void
    {
        $this->unsetAll();
        session_destroy();
    }

    /**
     * regenerate the session ID
     * This avoids session ghosting
     */
    public function regenerateSessionId():void
    {
        session_regenerate_id();
    }

}