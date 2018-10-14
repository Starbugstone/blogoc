<?php

namespace Core\Dependency;

class Cookie{

    /**
     * set a cookie
     * @param string $name
     * @param string $value
     * @param string $expireDate
     */
    public function setCookie(string $name, string $value, string $expireDate)
    {
        setcookie($name, $value, $expireDate, "/");
    }

    /**
     * delete a named cookie
     * @param string $name
     */
    public function deleteCookie(string $name)
    {
        setcookie($name, "", time()-3600); //expire the cookie
    }

    /**
     * get a cookie
     * @param string $name
     * @return bool
     */
    public function getCookie(string $name)
    {
        return $_COOKIE[$name] ?? false;

    }
}