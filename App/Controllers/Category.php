<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\SlugModel;
use Core\Container;
use Core\Controller;

class Category extends Controller
{

    protected $siteConfig;
    protected $pagination;

    private $slugModel;
    private $postModel;
    private $categoryModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        $this->loadModules[] = 'pagination';
        parent::__construct($container);
        $this->slugModel = new SlugModel($this->container);
        $this->postModel = new PostModel($this->container);
        $this->categoryModel = new CategoryModel($this->container);

    }

    /**
     * show all posts in a category
     * @param string $categorySlug the slug passed via url
     * @param string $page
     * @throws \ErrorException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function posts(string $categorySlug, string $page = "page-1")
    {


        $categoryId = $this->slugModel->getIdFromSlug($categorySlug, "categories", "categories_slug", "idcategories");
        $totalPosts = $this->postModel->totalNumberPostsInCategory($categoryId);

        $pagination = $this->pagination->getPagination($page, $totalPosts);

        $this->data['configs'] = $this->siteConfig->getSiteConfig();


        $this->data['navigation'] = $this->categoryModel->getMenu();
        $this->data['posts'] = $this->postModel->getPostsInCategory($categoryId, $pagination["offset"]);
        $this->data['pagination'] = $pagination;
        $this->data['categorySlug'] = $categorySlug;

        $this->renderView('Category');

    }

    public function allPosts(string $page = "page-1")
    {
        $totalPosts = $this->postModel->totalNumberPosts();

        $pagination = $this->pagination->getPagination($page, $totalPosts);

        $this->sendSessionVars();

        $this->data['configs'] = $this->siteConfig->getSiteConfig();


        $this->data['navigation'] = $this->categoryModel->getMenu();
        $this->data['posts'] = $this->postModel->getPosts($pagination["offset"]);
        $this->data['pagination'] = $pagination;

        $this->renderView('Category');
    }
}