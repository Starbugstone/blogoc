<?php

namespace App\Controllers;

use Core\Container;
use Core\Controller;
use Core\Traits\PasswordFunctions;

//This is just for testing purposes. a real login system shall be set up later

class Login extends Controller
{
    use PasswordFunctions;

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
        //check if have prefilled form data
        $this->sendSessionVars();
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $this->siteConfig->getMenu();
        $this->data["registrationInfo"] = $this->session->get("registrationInfo");

        $this->renderView('register');
    }

    /**
     * The post connection method
     */
    public function connection()
    {
        //is post
        if (!$this->request->isPost()) {
            $this->alertBox->setAlert('Only post messages allowed', 'error');
            $this->response->redirect('/');
        }
    }

    /**
     * the post registration method
     */
    public function registration()
    {
        //is post
        if (!$this->request->isPost()) {
            $this->alertBox->setAlert('Only post messages allowed', 'error');
            $this->response->redirect('/login/register');
        }

        $register = $this->request->getDataFull();


        //Error checking
        $error = false;
        if ($register["name"] == "") {
            $error = true;
            $this->alertBox->setAlert("name must not be empty", 'error');
        }
        if ($register["email"] == "") {
            $error = true;
            $this->alertBox->setAlert("email must not be empty", 'error');
        }
        if (!filter_var($register["email"], FILTER_VALIDATE_EMAIL)) {
            $error = true;
            $this->alertBox->setAlert("email is not valid", 'error');
            $register["email"] = "";
        }
        if ($register["username"] == "") {
            $error = true;
            $this->alertBox->setAlert("username must not be empty", 'error');
        }

        if ($register["password"] == "") {
            $error = true;
            $this->alertBox->setAlert("password must not be empty", 'error');
            $register["confirm"] = "";
        }
        if ($register["confirm"] == "") {
            $error = true;
            $this->alertBox->setAlert("password confirmation must not be empty", 'error');
            $register["password"] = "";
        }
        if ($register["confirm"] != $register["password"]) {
            $error = true;
            $this->alertBox->setAlert("Password and confirmation do not match", 'error');
            $register["password"] = "";
            $register["confirm"] = "";
        }
        $passwordError = $this->isPasswordComplex($register["password"]);
        if (!$passwordError["success"]) {
            $error = true;
            $this->alertBox->setAlert($passwordError["message"], 'error');
            $register["password"] = "";
            $register["confirm"] = "";
        }

        //If we found an error, return data to the register form and no create
        if ($error) {
            $this->session->set("registrationInfo", $register);
            $this->response->redirect('/login/register');
        }

        echo("registering<br>");
        var_dump($register);
        die();
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
        $this->session->set('user_id', 1);
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