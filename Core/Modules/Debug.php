<?php

namespace Core\Modules;

use Core\Constant;
use \Core\Container;

class Debug extends Module{

    /**
     * Var dump a var
     * @param $string
     */
    public function dump($var)
    {
        echo ("<pre>");
        var_dump($var);
        echo ("</pre>");
        die();
    }
}