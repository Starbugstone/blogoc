<?php

namespace App\Controllers;

use App\Models\UserModel;
use Core\BlogocException;
use Core\Config;
use Core\Container;
use Core\Controller;
use Core\Traits\PasswordFunctions;
use Core\Traits\StringFunctions;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

//This is just for testing purposes. a real login system shall be set up later

class Login extends Controller
{
    use PasswordFunctions;
    use StringFunctions;

    protected $siteConfig;
    private $userModel;

    private $user;

    private $mailer;
    private $transport;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        parent::__construct($container);

        $this->userModel = new UserModel($this->container);
        $this->user = new \stdClass();

        // Create the Transport for mail sending
        $config = $this->siteConfig->getSiteConfig();
        $this->transport = (new Swift_SmtpTransport($config["SMTP_server"], (int)$config["SMTP_port"]))
            ->setUsername($config["SMTP_user"])
            ->setPassword($config["SMTP_pass"])
        ;

        // Create the Mailer using your created Transport
        $this->mailer = new Swift_Mailer($this->transport);
    }

    /**
     * reset the local object user to an empty state
     */
    private function resetUser()
    {
        foreach (get_class_vars(get_class($this->user)) as $key => $value) {
            unset($this->user->$key);
        }
    }

    /**
     * add all the elements passed to the user object
     * @param array $userElements
     */
    private function populateUser(array $userElements)
    {
        //reset user info
        $this->resetUser();
        foreach ($userElements as $key => $element) {
            $this->user->$key = $element;
        }
    }

    /**
     * get user info from the database and populate the user object
     * @param int $userId
     * @throws \Exception
     */
    private function populateUserFromId(int $userId)
    {
        $result = $this->userModel->getUserDetailsById($userId);
        $this->populateUser((array)$result);
    }

    /**
     * pass the user object to the session for use
     */
    private function setUserSession()
    {
        $this->session->regenerateSessionId(); //regenerate the ID to avoid session ghosting
        $this->session->set("user", $this->user);
        $userRoleName = $this->user->role_name ?? "";
        $userRoleLevel = $this->user->role_level ?? 0;
        $this->session->set('user_role_name', $userRoleName);
        $this->session->set('user_role_level', $userRoleLevel);
    }

    /**
     * the login form
     */
    public function index()
    {
        if($this->session->isParamSet("user"))
        {
            //we are already connected, redirect
            $this->response->redirect();
        }

        $this->sendSessionVars();
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $this->siteConfig->getMenu();

        //check if have prefilled form data and error mesages
        $this->data["loginInfo"] = $this->session->get("loginInfo");
        $this->data["loginErrors"] = $this->session->get("loginErrors");

        //Setting a tag to deactivate modules on this page
        $this->data['onRegistrationPage'] = true;

        //remove the set data as it is now sent to the template
        $this->session->remove("loginInfo");
        $this->session->remove("loginErrors");

        $this->renderView('logon');
    }

    /**
     * the register form
     */
    public function register()
    {
        if($this->session->isParamSet("user"))
        {
            //we are already connected, redirect
            $this->response->redirect();
        }

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

        $login = $this->request->getDataFull();
        $email = $login["loginEmail"];
        $password = $login["loginPassword"];

        //resetting the password as we don't want to resend it
        $login["loginPassword"] = "";

        //check all the fields
        $error = false;
        $loginErrors = new \stdClass();
        $authUser = new \stdClass();

        if ($password == "") {
            $error = true;
            $loginErrors->password = "password must not be empty";
        }

        try {
            if (!$this->userModel->isEmailUsed($email)) {
                $error = true;
                $loginErrors->email = "This email is not registerd";
            }

            $authUser = $this->userModel->authenticateUser($email, $password);
            if(!$authUser->success)
            {
                $error = true;
                $loginErrors->global = $authUser->message;
            }
        } catch (BlogocException $e) { //this usually throws if mail isn't valid
            $error = true;
            $loginErrors->email = $e->getMessage();
        }

        if ($error) {
            $this->session->set("loginInfo", $login);
            $this->session->set("loginErrors", $loginErrors);
            $this->response->redirect("/login");
        }

        //we are authenticated here

        //populate the user object with returned data
        $this->populateUser((array)$authUser->user);
        $this->setUserSession();

        //if all is valid, redirect to user admin page
        $this->response->redirect("/admin");
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

        //Storing the passed information
        $this->populateUser($register);

        //removing the password from the return
        $register["password"] = "";
        $register["confirm"] = "";

        //Error checking

        //check all the fields
        $error = false;
        $registerErrors = new \stdClass();

        //if mail already used, go to login
        try {
            if ($this->userModel->isEmailUsed($this->user->email)) {
                $this->alertBox->setAlert("Email already registered, try logging in. You can always use the forgotten password to reset your account",
                    'error');
                $this->response->redirect('/login');
            }
        } catch (BlogocException $e) {
            $error = true;
            $registerErrors->email = $e->getMessage();
        }

        if ($this->user->name == "") {
            $error = true;
            $registerErrors->name = "name must not be empty";
        }
        if ($this->user->surname == "") {
            $error = true;
            $registerErrors->surname = "surname must not be empty";
        }
        if ($this->user->username == "") {
            $error = true;
            $registerErrors->username = "username must not be empty";
        }

        //checking the password
        $passwordError = $this->isPasswordComplex($this->user->password);
        if (!$passwordError["success"]) {
            $error = true;
            $registerErrors->password = $passwordError["message"];
        }
        if ($this->user->confirm !== $this->user->password) {
            $error = true;
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
        $userId = $this->userModel->registerUser($this->user);

        //repopulate our user with data from the database this will remove the password as we will no longer need it
        $this->populateUserFromId($userId);

        //get the unique hash for email validation


        //send confirmation mail



        //all set, redirect and set message
        $refererUrl = $this->request->getReferer();
        $baseUrl = $this->request->getBaseUrl();
        $redirectUrl = $this->removeFromBeginning($refererUrl, $baseUrl);

        $this->alertBox->setAlert('Account created, please check your mailbox to activate account');
        $this->response->redirect($redirectUrl);
    }

    /**
     * disconnect from the user interface
     */
    public function disconnect()
    {
        $this->session->destroySession();
        $this->alertBox->setAlert('Disconnected');
        $this->response->redirect();
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



    public function whoami()
    {
        $userType = $this->auth->getUser();
        if (is_null($userType)) {
            $userType = 'Not Set';
        }
        $this->alertBox->setAlert($userType);
        $this->container->getResponse()->redirect();
    }

    public function testMail()
    {

        // Create a message
        $message = (new Swift_Message('Wonderful Subject2'))
            ->setFrom(['john@doe.com' => 'John Doe'])
            ->setTo(['receiver@domain.org', 'other@domain.org' => 'A name'])
            ->setBody('Here is the message itself')
        ;

        // Send the message
        $mailResult = $this->mailer->send($message);
        var_dump($mailResult);
        die();
    }
}