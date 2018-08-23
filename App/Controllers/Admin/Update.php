<?php
namespace App\Controllers\Admin;

use App\Models\ConfigModel;
use Core\AdminController;
use Core\Traits\StringFunctions;


class Update extends AdminController {

    use StringFunctions;

    public function index(){
        echo'TEST';
    }

    public function updateConfig(){
        $this->onlyAdmin();
        if(!$this->container->getRequest()->isPost()){
            $this->alertBox->setAlert('Only post messages allowed', 'error');
            $this->container->getResponse()->redirect('admin');
        }
        $configModel = new ConfigModel($this->container);
        $posts = $this->container->getRequest()->getDataFull();

        foreach($posts as $key=>$config){
            $configid = $this->removeFromBeginning($key,'config-');
            $configModel->updateConfig($configid, $config);
        }
        echo '<pre>';
        var_dump($_POST);
        var_dump($_SESSION);
        die();
    }
}