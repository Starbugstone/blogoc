<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\PostModel;
use Core\Constant;
use Core\Container;
use Core\Controller;

class Category extends Controller
{

    protected $siteConfig;

    private $postModel;
    private $categoryModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        parent::__construct($container);
        $this->postModel = new PostModel($this->container);
        $this->categoryModel = new CategoryModel($this->container);

        $this->sendSessionVars();

    }

    /**
     * show all posts in a category
     * @param string $categorySlug the slug passed via url
     * @param string $page
     * @param int $linesPerPage
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function posts(string $categorySlug, string $page = "page-1", int $linesPerPage = Constant::POSTS_PER_PAGE)
    {
        $categoryId = $this->categoryModel->getCategoryIdFromSlug($categorySlug);
        $totalPosts = $this->postModel->totalNumberPostsInCategory($categoryId);
        $pagination = $this->pagination->getPagination($page, $totalPosts);

        if ($linesPerPage !== Constant::POSTS_PER_PAGE) {
            $this->data['paginationPostsPerPage'] = $linesPerPage;
        }

        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $this->siteConfig->getMenu();
        $this->data['posts'] = $this->postModel->getPostsInCategory($categoryId, $pagination["offset"], $linesPerPage);
        $this->data['pagination'] = $pagination;
        $this->data['categorySlug'] = $categorySlug;
        $this->data['category'] = $this->categoryModel->getCategoryDetails($categoryId);

        $this->renderView('Category');

    }

    /**
     * list all the posts no matter what category
     * @param string $page
     * @param int $linesPerPage
     * @throws \ErrorException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function allPosts(string $page = "page-1", int $linesPerPage = Constant::POSTS_PER_PAGE)
    {
        $totalPosts = $this->postModel->totalNumberPosts();
        $pagination = $this->pagination->getPagination($page, $totalPosts, $linesPerPage);

        if ($linesPerPage !== Constant::POSTS_PER_PAGE) {
            $this->data['paginationPostsPerPage'] = $linesPerPage;
        }

        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $this->siteConfig->getMenu();
        $this->data['posts'] = $this->postModel->getPosts($pagination["offset"], [], $linesPerPage);
        $this->data['pagination'] = $pagination;

        $this->renderView('Category');
    }
}