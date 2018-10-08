<?php
namespace App\Controllers;

use App\Models\PostModel;
use App\Models\UserModel;
use Core\Controller;
use Core\Container;

class Author extends Controller{


    protected $siteConfig;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        parent::__construct($container);
    }

    /**
     * get all posts from author
     * @param $authorId
     * @param string $page
     * @throws \ErrorException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function posts(int $authorId, string $page = "page-1")
    {
        $postModel = new PostModel($this->container);
        $userModel = new UserModel($this->container);

        $totalPosts = $postModel->totalNumberPostsByAuthor($authorId);
        $pagination = $this->pagination->getPagination($page, $totalPosts);

        $this->sendSessionVars();
        $this->data['posts'] = $postModel->getPostsWithAuthor($authorId, $pagination["offset"]);
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $this->siteConfig->getMenu();
        $this->data['pagination'] = $pagination;
        $this->data['authorId'] = $authorId;
        $this->data['user'] = $userModel->getAuthorDetails($authorId);

        $this->renderView('Author');
    }
}