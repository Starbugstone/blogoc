<?php

namespace Core;

class Session
{

    /**
     * Session class to take care of all the session details
     *
     * PHP version 7
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

    public function remove($param):void{
        unset($_SESSION[$param]);
    }
}