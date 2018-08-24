<?php

namespace App\Controllers\Admin;

use App\Models\ConfigModel;
use Core\AdminController;
use Core\Traits\StringFunctions;


class Update extends AdminController
{

    use StringFunctions;

    /**
     * gets the information in post and sets the corresponding site config details
     * this is pure php with no ajax
     * @throws \Exception
     */
    public function updateConfig()
    {
        $this->onlyAdmin();
        if (!$this->container->getRequest()->isPost()) {
            $this->alertBox->setAlert('Only post messages allowed', 'error');
            $this->container->getResponse()->redirect('admin');
        }
        $configModel = new ConfigModel($this->container);
        $posts = $this->container->getRequest()->getDataFull();

        $success = true;
        foreach ($posts as $key => $config) {
            $configId = $this->removeFromBeginning($key, 'config-');
            if (!$configModel->updateConfig($configId, $config)) {
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