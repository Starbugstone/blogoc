<?php

namespace App\Controllers\Admin;

use App\Models\ConfigModel;
use Core\AdminController;
use Core\Traits\StringFunctions;

/**
 * all to do with the config page
 * Class Config
 * @package App\Controllers\Admin
 */
class Config extends AdminController
{

    use StringFunctions;

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


    /**
     * gets the information in post and sets the corresponding site config details
     * this is pure php with no ajax
     * @throws \Exception
     */
    public function updateConfig()
    {
        //Security checks
        $this->onlyAdmin();
        if (!$this->container->getRequest()->isPost()) {
            $this->alertBox->setAlert('Only post messages allowed', 'error');
            $this->container->getResponse()->redirect('admin');
        }

        $configModel = new ConfigModel($this->container);
        $posts = $this->container->getRequest()->getDataFull();
        $success = true;

        foreach ($posts as $key => $config) {
            if (!$configModel->updateConfig($key, $config)) {
                $success = false;
            }
        }
        if ($success) {
            $this->alertBox->setAlert('Configuration updates successfully');
        } else {
            $this->alertBox->setAlert('error in configuration update', 'error');
        }
        $this->container->getResponse()->redirect('admin/config');
    }
}
