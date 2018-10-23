<?php

namespace Core\Dependency;

class Cookie{

    /**
     * set a cookie
     * @param string $name
     * @param string $value
     * @param int $expireDate
     */
    public function setCookie(string $name, string $value, int $expireDate):void
    {
        setcookie($name, $value, $expireDate, "/");
    }

    /**
     * delete a named cookie
     * @param string $name
     */
    public function deleteCookie(string $name):void
    {
        setcookie($name, "", time()-3600); //expire the cookie
    }

    /**
     * get a cookie
     * @param string $name
     * @return mixed
     */
    public function getCookie(string $name)
    {
        return $_COOKIE[$name] ?? false;

    }
}