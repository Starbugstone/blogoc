<?php

namespace App\Controllers\Admin;

use App\Models\CommentModel;
use App\Models\ConfigModel;
use Core\AdminController;
use Core\Traits\StringFunctions;
use Core\Container;

/**
 * all to do with the config page
 * Class Config
 * @package App\Controllers\Admin
 */
class Config extends AdminController
{

    use StringFunctions;
    protected $siteConfig;
    private $configModel;
    private $commentModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        parent::__construct($container);
        $this->configModel = new ConfigModel($this->container);
        $this->commentModel = new CommentModel($container);
        $this->data["pendingCommentsCount"] = $this->commentModel->countPendingComments();
    }

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

        $this->data['configList'] = $this->configModel->getAllConfigOrdered();
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
        $this->onlyPost();

        $posts = $this->request->getDataFull();
        $success = true;

        foreach ($posts as $key => $config) {
            if (!$this->configModel->updateConfig($key, $config)) {
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
