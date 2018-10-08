<?php

namespace App\Controllers;

use Core\Container;
use Core\Controller;

//This is just for testing purposes. a real login system shall be set up later

class Login extends Controller
{

    protected $siteConfig;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        parent::__construct($container);
    }


    /**
     * the login form
     */
    public function index()
    {
        $this->sendSessionVars();
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $this->siteConfig->getMenu();

        $this->renderView('logon');
    }

    /**
     * the register form
     */
    public function register()
    {
        $this->sendSessionVars();
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $this->siteConfig->getMenu();

        $this->renderView('register');
    }

    /**
     * The post connection method
     */
    public function connection()
    {
        //is post
    }

    /**
     * the post registration method
     */
    public function registration()
    {
        //is post
    }

    /*
     *-----------------------------------------------------------------------------------------
     *                     Temp connections for testing
     *-----------------------------------------------------------------------------------------
     */

    //all the connects will finaly be got in a single function grabbed from the DB / Session.
    //this is just for testing purposes until the core framework is finished
    public function connectAdmin()
    {
        $this->session->set('user_role_name', 'Admin');
        $this->session->set('user_role_level', 2);
        $this->session->set('user_id',1);
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