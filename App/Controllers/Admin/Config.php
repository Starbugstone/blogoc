<?php

namespace App\Controllers\Admin;

use App\Models\ConfigModel;
use Core\AdminController;

/**
 * all to do with the config page
 * Class Config
 * @package App\Controllers\Admin
 */
class Config extends AdminController
{
    /**
     * Shows the config page with all of the config options
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \ReflectionException
     */
    public function index()
    {
        $this->onlyAdmin();



        $configObj = new ConfigModel($this->container);
        $this->data['configList'] = $configObj->getAllConfigOrdered();



        $this->renderView('Admin/Config');
    }
}
