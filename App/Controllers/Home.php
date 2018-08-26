<?php

namespace App\Controllers;

use App\Models\ConfigModel;
use App\Models\IncludesModel;
use App\Models\PostModel;
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
        $frontPostModel = new PostModel($this->container);
        $frontPosts = $frontPostModel->getFrontPosts();

        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $Includes = new IncludesModel($this->container);
        $this->data['navigation'] = $Includes->getMenu();
        $this->data['jumbotron'] = true;
        $this->data['front_posts'] = $frontPosts;
        $this->renderView('Home');
    }
}