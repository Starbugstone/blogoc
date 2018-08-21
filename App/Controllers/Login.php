<?php

namespace App\Controllers;

use Core\Container;
use Core\Controller;

class Login extends Controller {

    protected $auth;
    public function __construct(Container $container)
    {
        $this->loadModules[] = 'Auth';
        parent::__construct($container);
    }

    //all the connects will finaly be got in a single function grabbed from the DB / Session.
    //this is just for testing purposes until the core framework is finished
    public function connectAdmin() {
        $this->session->set('session_level', 'Admin');
        $this->alertBox->setAlert('Connected as admin');
        $this->container->getResponse()->redirect('/admin/');
    }

    public function connectUser() {
        $this->session->set('session_level', 'User');
        $this->alertBox->setAlert('Connected as User');
        $this->container->getResponse()->redirect('/admin/');
    }

    public function connectVisitor() {
        $this->session->set('session_level', 'Shit'); //just ot override any other setting, and fun !!!
        $this->alertBox->setAlert('not a valid session. Visitor status by default');
        $this->container->getResponse()->redirect();
    }

    public function disconnect() {
        $this->container->getSession()->unsetAll();
        $this->alertBox->setAlert('Disconnected');
        $this->container->getResponse()->redirect();
    }

    public function whoami(){
        $userType = $this->auth->getUser();
        if (is_null($userType)){
            $userType = 'Not Set';
        }
        $this->alertBox->setAlert($userType);
        $this->container->getResponse()->redirect();
    }
}