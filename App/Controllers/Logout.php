<?php

namespace App\Controllers;

use Core\Controller;

/**
 * the logout page
 * Class Logout
 * @package App\Controllers
 */
class Logout extends Controller
{
    public function index()
    {
        $this->container->getSession()->unsetAll();
    }
}