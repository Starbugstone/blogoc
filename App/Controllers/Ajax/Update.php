<?php

namespace App\Controllers\Ajax;

use App\Models\ConfigModel;
use Core\AjaxController;
use Core\JsonException;
use Core\Traits\StringFunctions;

class Update extends AjaxController
{
    use StringFunctions;

    /**
     * Update the site configuration via Ajax post
     * @throws JsonException
     */
    public function updateConfig()
    {
        //security checks
        $this->onlyAdmin();
        if (!$this->container->getRequest()->isPost()) {
            throw new JsonException('Call is not post');
        }
        //prepating our return results
        $result = array();
        $result['successId'] = [];
        $result['errorId'] = [];
        $result['success'] = true;
        $configUpdateJson = $this->container->getRequest()->getData('config-update');
        $configUpdate = json_decode($configUpdateJson);

        $configModel = new ConfigModel($this->container);

        foreach ($configUpdate as $update) {

            if (!$configModel->updateConfig($update->dbId, $update->value)) {
                $result['success'] = false;
                $result['errorId'][] = $update->id;
            } else {
                $result['successId'][] = $update->id;
            }
        }

        echo json_encode($result);
    }
}