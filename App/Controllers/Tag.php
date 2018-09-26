<?php
namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\PostModel;
use Core\Controller;
use Core\Container;

class Tag extends Controller{

    protected $siteConfig;
    protected $pagination;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        $this->loadModules[] = 'pagination';
        parent::__construct($container);
    }

    /**
     * @param int $tagId
     * @param string $page
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function posts(int $tagId, string $page = "page-1")
    {
        $categoryModel = new CategoryModel($this->container);
        $postModel = new PostModel($this->container);

        $totalPosts = $postModel->totalNumberPostsByTag($tagId);
        $pagination = $this->pagination->getPagination($page, $totalPosts);

        $this->sendSessionVars();
        $this->data['posts'] = $postModel->getPostsWithTag($tagId, $pagination["offset"]);
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $categoryModel->getMenu();
        $this->data['pagination'] = $pagination;
        $this->data['tagId'] = $tagId;
        $this->renderView('Tag');
    }
}