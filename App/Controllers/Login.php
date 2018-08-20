<?php

namespace App\Controllers;

use Core\Controller;

class Login extends Controller {

    //all the connects will finaly be got it a single function grabbed from the DB / Session.
    //this is just for testing purposes until the core framework is finished
    public function connectAdmin(){
        $this->session->set('session_level', 'Admin');
        $this->container->getResponse()->redirect();
    }

    public function connectUser(){
        $this->session->set('session_level', 'User');
        $this->container->getResponse()->redirect();
    }

    public function connectVisitor(){
        $this->session->set('session_level', 'Shit'); //just ot override any other setting, and fun !!!
        $this->container->getResponse()->redirect();
    }

    public function disconnect(){
        $this->container->getSession()->unsetAll();
    }
}