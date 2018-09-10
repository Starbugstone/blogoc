<?php

namespace App\Controllers;

use App\Models\ConfigModel;
use App\Models\IncludesModel;
use Core\Container;

/**
 * Class Home
 *
 * The home page
 *
 * @package App\Controllers
 */
class Home extends \Core\Controller
{

    protected $siteConfig;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        parent::__construct($container);
    }

    public function index()
    {

        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $Includes = new IncludesModel($this->container);
        $this->data['navigation'] = $Includes->getMenu();
        $this->data['jumbotron'] = true;
        $this->renderView('Home');
    }
}