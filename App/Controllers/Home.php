<?php

namespace App\Controllers;

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

    /**
     * Show the front page
     * @throws \ErrorException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index()
    {
        $frontPostModel = new PostModel($this->container);

        $frontPosts = $frontPostModel->getFrontPosts();

        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $this->siteConfig->getMenu();
        $this->data['jumbotron'] = true;
        $this->data['front_posts'] = $frontPosts;

        $this->renderView('Home');
    }
}