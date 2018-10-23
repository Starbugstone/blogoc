<?php

namespace App\Controllers;

use App\Models\Remembered_loginModel;
use App\Models\UserModel;
use Core\BlogocException;
use Core\Container;
use Core\Controller;
use Core\Traits\PasswordFunctions;
use Core\Traits\StringFunctions;


class Login extends Controller
{
    use PasswordFunctions;
    use StringFunctions;

    protected $siteConfig;
    protected $sendMail;

    private $userModel;
    private $rememberedLoginModel;

    private $user;


    /**
     * Login constructor.
     * @param Container $container
     * @throws \ErrorException
     * @throws \ReflectionException
     */
    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        $this->loadModules[] = 'SendMail';
        parent::__construct($container);

        $this->userModel = new UserModel($this->container);
        $this->rememberedLoginModel = new Remembered_loginModel($this->container);
        $this->user = new \stdClass();

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
        $this->session->set("userId", $this->user->idusers);
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
        if ($this->session->isParamSet("user")) {
            //we are already connected, redirect
            $this->response->redirect();
        }

        $this->sendSessionVars();
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $this->siteConfig->getMenu();

        //check if have prefilled form data and error messages
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
        if ($this->session->isParamSet("user")) {
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
        $this->onlyPost();

        $login = $this->request->getDataFull();
        $email = $login["loginEmail"];
        $password = $login["loginPassword"];
        $rememberMe = isset($login["rememberMe"]);

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
                $loginErrors->email = "This email is not registered";
            }

            $authUser = $this->userModel->authenticateUser($email, $password);
            if (!$authUser->success) {
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

        //if the user wanted to be remembered
        if ($rememberMe) {
            $this->rememberedLoginModel->setToken(); //generate a new token
            $rememberMeToken = $this->rememberedLoginModel->rememberMe($this->user->idusers);
            if ($rememberMeToken->success) {
                //set cookie
                $this->cookie->setCookie("rememberMe", $rememberMeToken->token, $rememberMeToken->expiry_timestamp);

            }
        }

        //if all is valid, set the session and redirect to user admin page
        $this->setUserSession();
        $this->response->redirect("/admin");
    }

    /**
     * the post registration method
     */
    public function registration()
    {
        //is post
        $this->onlyPost();

        $register = $this->request->getDataFull();

        if($register === null)
        {
            throw new \Exception("Error no data passed");
        }

        //Storing the passed information
        $this->populateUser($register);

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
        $token = $this->userModel->generatePasswordHash($userId);

        //send confirmation mail
        $this->sendMail->sendNewPasswordMail($this->user->email, $token, $userId);


        //all set, redirect and set message
        $redirectUrl = "";
        $refererUrl = $this->request->getReferer();
        if ($refererUrl != "")//getReferer can return null if client isn't configured
        {
            $baseUrl = $this->request->getBaseUrl();
            $redirectUrl = $this->removeFromBeginning($refererUrl, $baseUrl);
        }


        if ($redirectUrl === "login/register") {
            //if we were already on the register page, go to home page
            $redirectUrl = "";
        }

        $this->alertBox->setAlert('Account created, please check your mailbox to activate account');
        $this->response->redirect($redirectUrl);
    }

    /**
     * disconnect from the user interface
     */
    public function disconnect()
    {
        $userId = $this->session->get("userId");
        if ($userId) {
            $userHash = $this->rememberedLoginModel->getTokenHashFromId($userId);
            $this->rememberedLoginModel->deleteToken($userHash);
        }


        $this->cookie->deleteCookie("rememberMe");
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
        $this->session->set('userId', 1);
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
}