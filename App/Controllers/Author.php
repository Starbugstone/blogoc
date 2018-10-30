<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\UserModel;
use Core\Constant;
use Core\Controller;
use Core\Container;

class Author extends Controller
{


    protected $siteConfig;

    private $postModel;
    private $userModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        parent::__construct($container);

        $this->postModel = new PostModel($this->container);
        $this->userModel = new UserModel($this->container);
    }

    /**
     * get all posts from author
     * @param int $authorId
     * @param string $page
     * @param int $linesPerPage
     * @throws \ErrorException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function posts(int $authorId, string $page = "page-1", int $linesPerPage = Constant::POSTS_PER_PAGE)
    {
        $totalPosts = $this->postModel->totalNumberPostsByAuthor($authorId);
        $pagination = $this->pagination->getPagination($page, $totalPosts);

        if ($linesPerPage !== Constant::POSTS_PER_PAGE) {
            $this->data['paginationPostsPerPage'] = $linesPerPage;
        }

        $this->sendSessionVars();
        $this->data['posts'] = $this->postModel->getPostsWithAuthor($authorId, $pagination["offset"], $linesPerPage);
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $this->siteConfig->getMenu();
        $this->data['pagination'] = $pagination;
        $this->data['authorId'] = $authorId;
        $this->data['user'] = $this->userModel->getUserDetailsById($authorId);

        $this->renderView('Author');
    }
}