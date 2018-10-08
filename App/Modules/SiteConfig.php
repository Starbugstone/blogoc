<?php

namespace App\Modules;

use App\Models\CategoryModel;
use Core\Modules\Module;
use App\Models\ConfigModel;

class SiteConfig extends Module
{
    /**
     * Gets the entire site configuration and arranges it into a displayable list
     * @return array the config ordered and ready to display
     * @throws \ReflectionException
     */
    public function getSiteConfig()
    {

        $configs = new ConfigModel($this->container);
        $siteConfig = $configs->getAllConfig();
        $data = [];
        foreach ($siteConfig as $config) {
            $data[$config->configs_name] = $config->configs_value;
        }
        return $data;
    }

    public function getMenu()
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
}