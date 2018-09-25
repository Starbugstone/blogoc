<?php
namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\SlugModel;
use App\Models\TagsModel;
use Core\Controller;
use Core\Container;

class Post extends Controller{

    protected $siteConfig;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        parent::__construct($container);
    }

    /**
     * @param $slug
     * @throws \ErrorException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function viewPost($slug){

        $tagModel = new TagsModel($this->container);
        $postModel = new PostModel($this->container);
        $slugModel = new SlugModel($this->container);
        $categoryModel = new CategoryModel($this->container);

        $postId = $slugModel->getIdFromSlug($slug, "posts", "posts_slug", "idposts");

        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['post'] = $postModel->getSinglePost($postId);
        $this->data['postTags'] = $tagModel->getTagsOnPost($postId);

        $this->data['navigation'] = $categoryModel->getMenu();
        $this->renderView('post');

    }
}