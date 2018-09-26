<?php
namespace App\Controllers;

use App\Models\CategoryModel;
use Core\Controller;
use Core\Container;

class Tag extends Controller{

    protected $siteConfig;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        parent::__construct($container);
    }

    public function posts($tagId)
    {
        $categoryModel = new CategoryModel($this->container);

        $this->sendSessionVars();
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $categoryModel->getMenu();
        $this->renderView('Tag');
    }
}