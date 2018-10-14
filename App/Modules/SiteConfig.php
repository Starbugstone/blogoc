<?php

namespace App\Modules;

use App\Models\CategoryModel;
use App\Models\UserModel;
use Core\Container;
use Core\Modules\Module;
use App\Models\ConfigModel;

class SiteConfig extends Module
{
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->loginFromRememberMe();
    }

    /**
     * Gets the entire site configuration and arranges it into a displayable list
     * @return array the config ordered and ready to display
     * @throws \ReflectionException
     */
    public function getSiteConfig():array
    {

        $configs = new ConfigModel($this->container);
        $siteConfig = $configs->getAllConfig();
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
        $categoryModel = new CategoryModel($this->container);

        $data = [];
        //get the categories from database
        $categories = $categoryModel->getCategories();
        foreach ($categories as $category) {
            $data += [
                $category->category_name => '/category/posts/' . $category->categories_slug
            ];
        }
        return $data;
    }

    public function loginFromRememberMe()
    {
        $cookie = $this->container->getCookie();
        $userModel = new UserModel($this->container);
        $session = $this->container->getSession();

        $userToken = $cookie->getCookie("rememberMe");
        if($userToken)
        {
            //we have a rememberMe Hash, login
            $rememberedLogin = $userModel->findByToken($userToken);
            if($rememberedLogin){
                //we have a hash, login
                $user = $userModel->getUserDetailsById($rememberedLogin->users_idusers);
                $session->regenerateSessionId(); //regenerate the ID to avoid session ghosting
                $session->set("user", $user);
                $userRoleName = $user->role_name ?? "";
                $userRoleLevel = $user->role_level ?? 0;
                $session->set('user_role_name', $userRoleName);
                $session->set('user_role_level', $userRoleLevel);
            }

        }

    }
}