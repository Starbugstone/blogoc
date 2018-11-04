<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\TagModel;
use Core\Constant;
use Core\Controller;
use Core\Container;

class Tag extends Controller
{

    protected $siteConfig;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        parent::__construct($container);
    }

    /**
     * @param int $tagId
     * @param string $page
     * @param int $linesPerPage
     * @throws \ErrorException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function posts(int $tagId, string $page = "page-1", int $linesPerPage = Constant::POSTS_PER_PAGE)
    {
        $postModel = new PostModel($this->container);
        $tagModel = new TagModel($this->container);

        $totalPosts = $postModel->totalNumberPostsByTag($tagId);
        if ($totalPosts < 1) {
            throw new \Exception("Tag has no posts", 404);
        }
        $pagination = $this->pagination->getPagination($page, $totalPosts, $linesPerPage);

        if ($linesPerPage !== Constant::POSTS_PER_PAGE) {
            $this->data['paginationPostsPerPage'] = $linesPerPage;
        }

        $this->sendSessionVars();
        $this->data['posts'] = $postModel->getPostsWithTag($tagId, $pagination["offset"]);
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['navigation'] = $this->siteConfig->getMenu();
        $this->data['pagination'] = $pagination;
        $this->data['tagId'] = $tagId;
        $this->data['tag'] = $tagModel->getTagDetails($tagId);
        $this->renderView('Tag');
    }
}