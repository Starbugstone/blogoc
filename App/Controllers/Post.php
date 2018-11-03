<?php

namespace App\Controllers;

use App\Models\CommentModel;
use App\Models\PostModel;
use App\Models\TagModel;
use Core\Constant;
use Core\Controller;
use Core\Container;

class Post extends Controller
{

    protected $siteConfig;
    protected $sendMail;

    private $commentModel;
    private $tagModel;
    private $postModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        $this->loadModules[] = 'SendMail';
        parent::__construct($container);
        $this->commentModel = new CommentModel($this->container);
        $this->tagModel = new TagModel($this->container);
        $this->postModel = new PostModel($this->container);

    }

    /**
     * @param string $slug
     * @param string $page
     * @param int $linesPerPage
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function viewPost(string $slug, string $page = "page-1", int $linesPerPage = Constant::COMMENTS_PER_PAGE)
    {

        $postId = (int)$this->postModel->getPostIdFromSlug($slug);

        $posts = $this->postModel->getSinglePost($postId);

        if($posts === false)
        {
            throw new \Exception("Page no longer exists", "404");
        }

        //only admins can view unpublished posts
        if (!$posts->published) {
            if (!$this->auth->isAdmin()) {
                throw new \Exception("File does not exist", "404");
            }
            $this->alertBox->setAlert('This post is not yet published', 'warning');
        }

        $totalComments = $this->commentModel->countCommentsOnPost($postId);
        $pagination = $this->pagination->getPagination($page, $totalComments, $linesPerPage);

        if ($linesPerPage !== Constant::COMMENTS_PER_PAGE) {
            $this->data['paginationPostsPerPage'] = $linesPerPage;
        }

        $this->sendSessionVars();
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['post'] = $posts;
        $this->data['postTags'] = $this->tagModel->getTagsOnPost($postId);
        $this->data['navigation'] = $this->siteConfig->getMenu();
        $this->data["comments"] = $this->commentModel->getCommentsListOnPost($postId, $pagination["offset"],
            $linesPerPage);
        $this->data['pagination'] = $pagination;

        $this->renderView('Post');

    }

    /**
     * Add a comment to the post
     * @throws \Exception
     */
    public function addComment()
    {
        $this->onlyPost();
        $this->onlyUser();

        //get the session userId
        $userId = (int)$this->session->get("userId");
        $comment = (string)$this->request->getData("newComment");
        $postId = (int)$this->request->getData("postId");

        //check if we are admin, Admins do not need moderation
        $admin = $this->session->get('user_role_level') >= Constant::ADMIN_LEVEL;
        $commentId = $this->commentModel->addComment($postId, $userId, $comment, $admin);

        if (!$admin) //if we are not an admin, send an email to alert and add an alertBox
        {
            $siteConfig = $this->siteConfig->getSiteConfig();
            $post = $this->postModel->getSinglePost($postId);
            $baseUrl = $this->request->getBaseUrl();

            $emailMessage = "<h1>New comment on post " . $post->title . "</a></h1>";
            $emailMessage .= "<p>Check it out <a href='" . $baseUrl . "admin/comments/moderate-comment/" . $commentId . "'>here</a> </p>";

            $this->sendMail->send($siteConfig["admin_email_address"], "New comment added", $emailMessage);

            $this->alertBox->setAlert("Your post will be published after moderation.");
        }

        $postSlug = $this->postModel->getPostSlugFromId($postId);

        $this->response->redirect("/post/view-post/" . $postSlug);
    }
}