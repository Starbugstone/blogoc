<?php
namespace App\Controllers\Ajax;

use App\Models\ConfigModel;
use Core\AjaxController;
use Core\JsonException;
use Core\Traits\StringFunctions;

class Update extends AjaxController{
    use StringFunctions;

    public function updateConfig()
    {
        $this->onlyAdmin();
        if (!$this->container->getRequest()->isPost()) {
            throw new JsonException('Call is not post');
        }
        $result = array();
        $result['successId'] = [];
        $result['errorId'] = [];
        $configUpdateJson = $this->container->getRequest()->getData('config-update');
        $configUpdate = json_decode($configUpdateJson);

        $configModel = new ConfigModel($this->container);

        $success = true;
        foreach ($configUpdate as $update){


            if (!$configModel->updateConfig($update->dbId, $update->value)) {
                $success = false;
                $result['errorId'][] = $update->id;
            }else{
                $result['successId'][] = $update->id;
            }
        }
        $result['success']=$success;

        echo json_encode($result);


        die();
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