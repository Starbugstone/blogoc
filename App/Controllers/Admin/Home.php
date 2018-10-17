<?php

namespace App\Controllers\Admin;



use App\Models\RoleModel;
use App\Models\UserModel;
use Core\Container;
use Core\Traits\StringFunctions;

class Home extends \Core\AdminController
{
    use StringFunctions;
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
        if(!$this->isInt($userId))
        {
            throw new \Exception("Error in passed ID");
        }

        $this->data["user"] = $this->userModel->getUserDetailsById($userId);

        $this->data["roles"] = $this->roleModel->getRoleList();

        $this->renderView('Admin/Home');
    }

    /**
     * Update the user info via post
     */
    public function updateUser()
    {
        $this->onlyPost();

    }
}