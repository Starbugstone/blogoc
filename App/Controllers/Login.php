<?php

namespace App\Controllers;

use App\Models\UserModel;
use Core\Container;
use Core\Controller;
use Core\Traits\PasswordFunctions;

//This is just for testing purposes. a real login system shall be set up later

class Login extends Controller
{
    use PasswordFunctions;

    protected $siteConfig;
    private $userModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        parent::__construct($container);

        $this->userModel = new UserModel($this->container);
    }


    /**
     * the login form
     */
    public function index()
    {
        $this->sendSessionVars();
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $this->siteConfig->getMenu();
        //Setting a tag to deactivate modules on this page
        $this->data['onRegistrationPage'] = true;

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

        //check if have prefilled form data and error mesages
        $this->data["registrationInfo"] = $this->session->get("registrationInfo");
        $this->data["registrationErrors"] = $this->session->get("registrationErrors");

        //We are on the registration page, deactivate bootstrap modals
        $this->data['onRegistrationPage'] = true;

        //remove the set data as it is now sent to the template
        $this->session->remove("registrationInfo");
        $this->session->remove("registrationErrors");

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

        //if all is valid, need to get the referer to redirect to same page
    }

    /**
     * the post registration method
     */
    public function registration()
    {
        //is post
        if (!$this->request->isPost()) {
            $this->alertBox->setAlert('Only post messages allowed', 'error');
            $this->response->redirect('/');
        }

        $register = $this->request->getDataFull();

        //Storing the password and confirmation since we will be doing multiple checks
        $sentPassword = $register["password"];
        $sentConfirmation = $register["confirm"];

        //Error checking

        //if mail already used, go to login
        if ($this->userModel->isEmailUnique($register["email"])) {
            $this->alertBox->setAlert("Email already registered, try logging in. You can always use the forgotten password to reset your account",
                'error');
            $this->response->redirect('/login');
        }

        //check all the fields
        $error = false;
        $registerErrors = new \stdClass();


        if ($register["name"] == "") {
            $error = true;
            $registerErrors->name = "name must not be empty";
        }
        if ($register["surname"] == "") {
            $error = true;
            $registerErrors->surname = "surname must not be empty";
        }
        if (!filter_var($register["email"], FILTER_VALIDATE_EMAIL)) {
            $error = true;
            $register["email"] = "";
            $registerErrors->email = "email is not valid";
        }
        if ($register["username"] == "") {
            $error = true;
            $registerErrors->username = "username must not be empty";
        }

        //checking the password
        $passwordError = $this->isPasswordComplex($sentPassword);
        if (!$passwordError["success"]) {
            $error = true;
            $register["password"] = "";
            $register["confirm"] = "";
            $registerErrors->password = $passwordError["message"];
        }
        if ($sentPassword == "") {
            $error = true;
            $register["confirm"] = "";
        }
        if ($sentConfirmation == "") {
            $error = true;
            $register["password"] = "";
        }
        if ($sentConfirmation !== $sentPassword) {
            $error = true;
            $register["password"] = "";
            $register["confirm"] = "";
            $registerErrors->password = "Password and confirmation do not match";
            $registerErrors->confirm = "Password and confirmation do not match";
        }

        //If we found an error, return data to the register form and no create
        if ($error) {
            $this->session->set("registrationInfo", $register);
            $this->session->set("registrationErrors", $registerErrors);
            $this->response->redirect('/login/register');
        }

        //From here, all should be good, register the user

        //send confirmation mail

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