<?php

namespace App\Modules;

use App\Models\CategoryModel;
use App\Models\Remembered_loginModel;
use App\Models\UserModel;
use Core\Container;
use Core\Modules\Module;
use App\Models\ConfigModel;
use Core\Traits\StringFunctions;

class SiteConfig extends Module
{
    use StringFunctions;

    private $configs;
    private $categoryModel;
    private $userModel;
    private $rememberedLoginModel;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->configs = new ConfigModel($this->container);
        $this->categoryModel = new CategoryModel($this->container);
        $this->userModel = new UserModel($this->container);
        $this->rememberedLoginModel = new Remembered_loginModel($this->container);

        $this->loginFromRememberMe();
    }

    /**
     * Gets the entire site configuration and arranges it into a displayable list
     * @return array the config ordered and ready to display
     * @throws \ReflectionException
     */
    public function getSiteConfig():array
    {


        $siteConfig = $this->configs->getAllConfig();
        $data = [];
        foreach ($siteConfig as $config) {
            $data[$config->configs_name] = $config->configs_value;
        }
        return $data;
    }

    /**
     * create the front end menu object to be sent to twig and add the urls
     * @return array
     * @throws \ReflectionException
     */
    public function getMenu():array
    {
        $data = [];
        //get the categories from database
        $categories = $this->categoryModel->getCategories();
        foreach ($categories as $category) {
            $data += [
                $category->category_name => '/category/posts/' . $category->categories_slug
            ];
        }
        return $data;
    }

    /**
     * auto login if the remember me cookie is set
     * @throws \Exception
     */
    public function loginFromRememberMe()
    {
        $cookie = $this->container->getCookie();
        $session = $this->container->getSession();
        $userToken = $cookie->getCookie("rememberMe");

        if($userToken && $this->isHexa($userToken))
        {
            //we have a rememberMe Hash, login
            $rememberedLogin = $this->rememberedLoginModel->findByToken($userToken);
            if($rememberedLogin){
                //we have a hash, login
                $user = $this->userModel->getUserDetailsById($rememberedLogin->users_idusers);
                $session->regenerateSessionId(); //regenerate the ID to avoid session ghosting
                $session->set("user", $user);
                $session->set("userId", $user->idusers);
                $userRoleName = $user->role_name ?? "";
                $userRoleLevel = $user->role_level ?? 0;
                $session->set('user_role_name', $userRoleName);
                $session->set('user_role_level', $userRoleLevel);
            }

        }

    }
}