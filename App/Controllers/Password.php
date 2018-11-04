<?php

namespace App\Controllers;

use App\Models\UserModel;
use Core\BlogocException;
use Core\Controller;
use Core\Container;
use Core\Traits\PasswordFunctions;
use Core\Traits\StringFunctions;

class Password extends Controller
{

    use PasswordFunctions;
    use StringFunctions;

    protected $siteConfig;
    protected $sendMail;

    private $userModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        $this->loadModules[] = 'SendMail';
        parent::__construct($container);

        $this->userModel = new UserModel($this->container);
    }

    public function index()
    {
        if ($this->session->isParamSet("user")) {
            //we are already connected, redirect
            $this->response->redirect();
        }

        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $this->siteConfig->getMenu();

        $this->renderView('forgotPassword');
    }

    public function reset($get)
    {
        //only get messages here
        if(!$this->startsWith(strtolower($get),"get"))
        {
            throw new \Exception("invalid call");
        }

        //grab the token and ID
        $token = $this->request->getData("token");
        $userId = $this->request->getData("userId");

        //verify if token is valid
        if(!$this->isHexa($token)|| !$this->isInt($userId))
        {
            $this->alertBox->setAlert('Badly formatted Token', 'error');
            $this->response->redirect();
        }
        if(!$this->userModel->getUserDetailsByToken($token, $userId))
        {
            $this->alertBox->setAlert('Invalid reset token, please request a new password', 'error');
            $this->response->redirect();
        }

        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $this->siteConfig->getMenu();

        $this->data["token"] = $token;
        $this->data["userId"] = $userId;
        $this->renderView('resetPassword');
    }

    public function resetPassword()
    {
        $this->onlyPost();
        $request = $this->request->getDataFull();
        $password = $request["forgotPassword"];
        $confirm = $request["forgotConfirm"];
        $token = $request["token"];
        $userId = $request["userId"];

        if(!$this->isHexa($token) || !$this->isInt($userId) )
        {
            throw new \Exception("Bad Token or ID request");
        }

        $error = false;
        $registerErrors = new \stdClass();
        if($password !== $confirm)
        {
            $error = true;
            $registerErrors->forgotPassword = "password and confirmation do not match";
            $registerErrors->forgotConfirm = "password and confirmation do not match";
        }

        $passwordError = $this->isPasswordComplex($password);
        if (!$passwordError["success"]) {
            $error = true;
            $registerErrors->forgotPassword = $passwordError["message"];
        }

        if ($error) {
            $this->session->set("registrationErrors", $registerErrors);
            $this->response->redirect('/password/reset/get?token='.$token);
        }

        $user = $this->userModel->getUserDetailsByToken($token, $userId);
        if (!$user) {

            $this->alertBox->setAlert('Invalid reset token', 'error');
            $this->response->redirect();
        }

        $this->userModel->resetPassword($user->idusers, $password);

        $this->alertBox->setAlert('Password reset, please login');
        $this->response->redirect("/login");

    }

    /**
     * @throws \Exception
     */
    public function sendResetMail()
    {
        $this->onlyPost();
        $request = $this->request->getDataFull();
        $email = $request["forgotEmail"];

        $error = false;
        $registerErrors = new \stdClass();
        $user = false;

        try {
            $user = $this->userModel->getUserDetailsByEmail($email);
            if (!$user) {
                $error = true;
                $registerErrors->forgotEmail = "email not found";
            }
        } catch (BlogocException $e) {
            $error = true;
            $registerErrors->forgotEmail = $e->getMessage();
        }

        if ($error) {
            $this->session->set("registrationInfo", $request);
            $this->session->set("registrationErrors", $registerErrors);
            $this->response->redirect('/password');
        }

        $token = $this->userModel->generatePasswordHash($user->idusers);
        $this->sendMail->sendResetPasswordMail($email, $token, $user->idusers);

        $this->alertBox->setAlert('Password reset link sent to your mailbox');
        $this->response->redirect();
    }


}