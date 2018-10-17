<?php

namespace App\Controllers\Admin;


use App\Models\RoleModel;
use App\Models\UserModel;
use Core\Container;
use Core\Traits\PasswordFunctions;
use Core\Traits\StringFunctions;

class Home extends \Core\AdminController
{
    use StringFunctions;
    use PasswordFunctions;
    protected $siteConfig;

    private $userModel;
    private $roleModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        parent::__construct($container);
        $this->userModel = new UserModel($this->container);
        $this->roleModel = new RoleModel($this->container);

        $this->data['configs'] = $this->siteConfig->getSiteConfig();
    }

    /**
     * The front page of the admin section. We display the user info
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index()
    {
        $this->onlyUser();

        //check if have prefilled form data and error mesages
        $this->data["registrationInfo"] = $this->session->get("registrationInfo");
        $this->data["registrationErrors"] = $this->session->get("registrationErrors");

        //remove the set data as it is now sent to the template
        $this->session->remove("registrationInfo");
        $this->session->remove("registrationErrors");

        $userId = $this->session->get("userId");
        $this->data["user"] = $this->userModel->getUserDetailsById($userId);

        $this->data["roles"] = $this->roleModel->getRoleList();

        $this->renderView('Admin/Home');
    }

    /**
     * Administrate a user as an admin
     * @param int $userId
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function viewUser(int $userId)
    {
        $this->onlyAdmin();
        if (!$this->isInt($userId)) {
            throw new \Exception("Error in passed ID");
        }

        //check if have prefilled form data and error mesages
        $this->data["registrationInfo"] = $this->session->get("registrationInfo");
        $this->data["registrationErrors"] = $this->session->get("registrationErrors");

        //remove the set data as it is now sent to the template
        $this->session->remove("registrationInfo");
        $this->session->remove("registrationErrors");

        $this->data["user"] = $this->userModel->getUserDetailsById($userId);

        $this->data["roles"] = $this->roleModel->getRoleList();

        $this->renderView('Admin/Home');
    }

    /**
     * Update the user info via post
     */
    public function updateUser()
    {
        $this->onlyUser();
        $this->onlyPost();

        $user = (object)$this->request->getDataFull();
        $redirectUrl = "/admin";

        if ($user->userId !== $this->session->get("userId") || isset($user->userRoleSelector)) {
            //an admin is trying to update a user or form tampered with
            $this->onlyAdmin();
            $redirectUrl = "/admin/home/view-user/" . $user->userId;
        } else {
            //set the role to the original state for update
            $beforeUser = $this->userModel->getUserDetailsById($user->userId);
            $user->userRoleSelector = $beforeUser->roles_idroles;
        }

        $userId = $user->userId;
        $password = $user->forgotPassword ?? "";
        $confirm = $user->forgotConfirm ?? "";
        $resetPassword = false;
        $error = false;
        $registerErrors = new \stdClass();

        if ($password !== "" || $confirm !== "") {
            //we are resetting the password
            $resetPassword = true;
            if ($password !== $confirm) {
                $error = true;
                $registerErrors->forgotPassword = "password and confirmation do not match";
                $registerErrors->forgotConfirm = "password and confirmation do not match";
            }

            $passwordError = $this->isPasswordComplex($password);
            if (!$passwordError["success"]) {
                $error = true;
                $registerErrors->forgotPassword = $passwordError["message"];
            }
        }

        if ($user->userName == "") {
            $error = true;
            $registerErrors->userName = "name must not be empty";
        }
        if ($user->userSurname == "") {
            $error = true;
            $registerErrors->userSurname = "surname must not be empty";
        }
        if ($user->userUsername == "") {
            $error = true;
            $registerErrors->userUsername = "username must not be empty";
        }

        if ($error) {
            $this->session->set("registrationErrors", $registerErrors);
            $this->response->redirect($redirectUrl);
        }

        if ($resetPassword) {
            $this->userModel->resetPassword($userId, $password);
        }

        $this->userModel->updateUser($user);

        $this->alertBox->setAlert('User details updated');
        $this->response->redirect($redirectUrl);
    }
}