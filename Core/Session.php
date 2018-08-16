<?php

namespace Core;

/**
 * Class Session
 * Session class to take care of all the session details
 *
 * @package Core
 *
 * PHP version 7
 */
class Session
{
    /**
     * Session constructor.
     */
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
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

        return $_SESSION[$param];
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
     * removes elements from the session
     * @param $param string parameter to remove
     */
    public function remove($param):void{
        unset($_SESSION[$param]);
    }
}