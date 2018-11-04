<?php

namespace App\Controllers\Ajax;

use App\Models\ConfigModel;
use Core\AjaxController;
use Core\Container;
use Core\JsonException;
use Core\Traits\StringFunctions;
use Swift_TransportException;

class Config extends AjaxController
{
    use StringFunctions;

    protected $sendMail;

    private $configModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SendMail';
        parent::__construct($container);
        $this->configModel = new ConfigModel($this->container);
    }

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

        foreach ($configUpdate as $update) {


            if (!$this->configModel->updateConfig($update->name, $update->value)) {
                $result['success'] = false;
                $result['errorId'][] = $update->name;
            } else {
                $result['successId'][] = $update->name;
            }
        }

        echo json_encode($result);
    }

    /**
     * Send a test mail
     * @throws JsonException
     */
    public function testMail(){
        $this->onlyAdmin();
        $this->onlyPost();
        $result = array();
        $result['success'] = false;
        try{
            $this->sendMail->sendTestMail();
            $result['success'] = true;
        }catch (Swift_TransportException $e) {
            $result['success'] = false;
        }

        echo json_encode($result);
    }
}