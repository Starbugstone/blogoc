<?php

namespace App\Controllers;

use Core\Container;
use Core\Controller;

//This is just for testing purposes. a real login system shall be set up later

class Login extends Controller
{

    protected $auth;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'Auth';
        parent::__construct($container);
    }

    //all the connects will finaly be got in a single function grabbed from the DB / Session.
    //this is just for testing purposes until the core framework is finished
    public function connectAdmin()
    {
        $this->session->set('user_role_name', 'Admin');
        $this->session->set('user_role_level', 2);
        $this->alertBox->setAlert('Connected as admin');
        $this->container->getResponse()->redirect('/admin/');
    }

    public function connectUser()
    {
        $this->session->set('user_role_name', 'User');
        $this->session->set('user_role_level', 1);
        $this->alertBox->setAlert('Connected as User');
        $this->container->getResponse()->redirect('/admin/');
    }

    public function disconnect()
    {
        $this->container->getSession()->unsetAll();
        $this->alertBox->setAlert('Disconnected');
        $this->container->getResponse()->redirect();
    }

    public function whoami()
    {
        $userType = $this->auth->getUser();
        if (is_null($userType)) {
            $userType = 'Not Set';
        }
        $this->alertBox->setAlert($userType);
        $this->container->getResponse()->redirect();
    }
}