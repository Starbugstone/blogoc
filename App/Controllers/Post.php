<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\TagModel;
use Core\Controller;
use Core\Container;

class Post extends Controller
{

    protected $siteConfig;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        parent::__construct($container);
    }

    /**
     * @param $slug
     * @throws \Exception
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function viewPost(string $slug)
    {

        $tagModel = new TagModel($this->container);
        $postModel = new PostModel($this->container);

        $postId = $postModel->getPostIdFromSlug($slug);

        $posts = $postModel->getSinglePost($postId);

        //only admins can view unpublished posts
        if (!$posts->published) {
            if (!$this->auth->isAdmin()) {
                throw new \Exception("File does not exist", "404");
            }
            $this->alertBox->setAlert('This post is not yet published', 'warning');
        }


        $this->sendSessionVars();
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['post'] = $posts;
        $this->data['postTags'] = $tagModel->getTagsOnPost($postId);
        $this->data['navigation'] = $this->siteConfig->getMenu();

        $this->renderView('post');

    }
}