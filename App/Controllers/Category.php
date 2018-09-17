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

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        parent::__construct($container);
    }

    /**
     * show all posts in a category
     * @param string $categorySlug the slug passed via url
     * @throws \ErrorException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function posts(string $categorySlug)
    {
        $slugModel = new SlugModel($this->container);
        $postModel = new PostModel($this->container);
        $categoryModel = new CategoryModel($this->container);

        $this->data['configs'] = $this->siteConfig->getSiteConfig();

        $categoryId = $slugModel->getIdFromSlug($categorySlug, "categories", "categories_slug", "idcategories");
        $this->data['navigation'] = $categoryModel->getMenu();
        $this->data['posts'] = $postModel->getPostsInCategory($categoryId);

        $this->renderView('Category');

    }
}