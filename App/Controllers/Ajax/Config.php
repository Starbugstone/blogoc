<?php

namespace App\Controllers\Ajax;

use App\Models\ConfigModel;
use Core\AjaxController;
use Core\JsonException;
use Core\Traits\StringFunctions;

class Config extends AjaxController
{
    use StringFunctions;

    /**
     * Update the site configuration via Ajax post
     * @throws JsonException
     */
    public function update()
    {
        //security checks
        $this->onlyAdmin();
        $this->onlyPost();
        //preparing our return results
        $result = array();
        $result['successId'] = [];
        $result['errorId'] = [];
        $result['success'] = true;
        $configUpdateJson = $this->container->getRequest()->getData('config-update');
        $configUpdate = json_decode($configUpdateJson);

        $configModel = new ConfigModel($this->container);

        foreach ($configUpdate as $update) {


            if (!$configModel->updateConfig($update->name, $update->value)) {
                $result['success'] = false;
                $result['errorId'][] = $update->name;
            } else {
                $result['successId'][] = $update->name;
            }
        }

        echo json_encode($result);
    }
}