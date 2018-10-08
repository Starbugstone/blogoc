<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\TagModel;
use Core\AdminController;
use Core\Constant;
use Core\Container;

class Post extends AdminController
{

    protected $siteConfig;
    protected $pagination;

    private $categoryModel;
    private $tagModel;
    private $postModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        $this->loadModules[] = 'pagination';
        parent::__construct($container);

        $this->categoryModel = new CategoryModel($this->container);
        $this->tagModel = new TagModel($this->container);
        $this->postModel = new PostModel($this->container);

        //adding the necessary default data
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['categories'] = $this->categoryModel->getCategories();
    }

    /**
     * add tags to a post
     * @param array $tags list of tags to apply
     * @param int $postId the post to add tags to
     * @throws \Exception
     */
    private function addTags(array $tags, int $postId):void
    {
        foreach ($tags as $tag) {
            if (isset($tag["id"])) {
                $this->tagModel->addTagToPost($postId, $tag["id"]);
                continue;
            }
            $this->tagModel->addNewTagToPost($postId, $tag["name"]);
        }
    }

    /**
     * page for new post
     */
    public function new()
    {
        $this->onlyAdmin();
        $this->data['tags'] = $this->tagModel->getTags();
        $this->renderView('Admin/Post');
    }

    /**
     * Lists all the posts
     * @param string $page
     * @param int $postsPerPage
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function list(string $page = "page-1", int $postsPerPage = Constant::LIST_PER_PAGE)
    {
        $this->onlyAdmin();

        $totalPosts = $this->postModel->totalNumberFullPosts();
        $pagination = $this->pagination->getPagination($page, $totalPosts, $postsPerPage);

        if($postsPerPage !== Constant::LIST_PER_PAGE){
            $this->data['paginationPostsPerPage'] = $postsPerPage;
        }

        $this->data["posts"] = $this->postModel->getFullPosts($pagination["offset"], $postsPerPage);
        $this->data['pagination'] = $pagination;
        $this->renderView("Admin/ListPost");
    }

    /**
     * Shows the post to modify and update
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \ErrorException
     */
    public function modify(string $slug): void
    {
        $this->onlyAdmin();

        $postId = $this->postModel->getPostIdFromSlug($slug, "posts", "posts_slug", "idposts");

        $this->data['post'] = $this->postModel->getSinglePost($postId);
        $this->data['postTags'] = $this->tagModel->getTagsOnPost($postId);
        $this->data['tags'] = $this->tagModel->getTags();
        $this->renderView('Admin/Post');
    }

    /**
     * Create a new post
     * @throws \Exception
     */
    public function createNewPost()
    {
        //Security checks
        $this->onlyAdmin();
        if (!$this->request->isPost()) {
            $this->alertBox->setAlert('Only post messages allowed', 'error');
            $this->response->redirect('admin');
        }

        $posts = $this->container->getRequest()->getDataFull();
        $userSessionId = $this->container->getSession()->get("user_id");


        $title = trim($posts["postTitle"]);
        $postImage = $posts["postImage"];
        $postSlug = trim($posts["postSlug"]);
        $article = $posts["postTextArea"];
        $idCategory = $posts["categorySelector"];
        $published = $posts["isPublished"];
        $onFrontPage = $posts["isOnFrontPage"];
        $idUser = $userSessionId;

        if(!is_int($idUser) || $idUser === null)
        {
            throw new \Error("Invalid userID");
        }

        //security and error checks
        $error = false;
        if ($title == "") {
            $error = true;
            $this->alertBox->setAlert("empty title not allowed", "error");
        }
        if ($postSlug == "") {
            $error = true;
            $this->alertBox->setAlert("empty slug not allowed", "error");
        }
        if (!$this->postModel->isPostSlugUnique($postSlug)) {
            $error = true;
            $this->alertBox->setAlert("Slug not unique", "error");
        }

        if ($error) {
            $this->container->getResponse()->redirect("admin/post/new");
        }

        $postId = $this->postModel->newPost($title, $postImage, $idCategory, $article, $idUser, $published, $onFrontPage,
            $postSlug);

        //Taking care of tags.
        if (isset($posts["tags"])) {
            $this->addTags($posts["tags"], $postId);
        }

        //checking result and redirecting
        if ($postId != null) {
            $this->alertBox->setAlert("Post " . $title . " Created");
            $this->container->getResponse()->redirect("admin/post/modify/" . $postSlug);
        }
        $this->alertBox->setAlert("Error creating " . $title, "error");
        $this->container->getResponse()->redirect("admin/post/new");

    }

    /**
     * update a post
     * @throws \Exception
     */
    public function modifyPost()
    {
        //Security checks
        $this->onlyAdmin();
        if (!$this->request->isPost()) {
            $this->alertBox->setAlert('Only post messages allowed', 'error');
            $this->response->redirect('admin');
        }

        $posts = $this->container->getRequest()->getDataFull();

        $postId = $posts["postId"];
        $title = trim($posts["postTitle"]);
        $postImage = $posts["postImage"];
        $postSlug = trim($posts["postSlug"]);
        $article = $posts["postTextArea"];
        $idCategory = $posts["categorySelector"];
        $published = $posts["isPublished"];
        $onFrontPage = $posts["isOnFrontPage"];

        //security and error checks
        $originalPostSlug = $this->postModel->getpostSlugFromId($postId);
        $error = false;
        if ($title == "") {
            $error = true;
            $this->alertBox->setAlert("empty title not allowed", "error");
        }

        if ($postSlug == "") {
            $error = true;
            $this->alertBox->setAlert("empty slug not allowed", "error");
        }

        if ($postSlug != $originalPostSlug) //if the slug has been updated
        {
            if (!$this->postModel->isPostSlugUnique($postSlug)) {
                $error = true;
                $originalPostSlug = $this->postModel->getPostSlugFromId($postId);
                $this->alertBox->setAlert("Slug not unique", "error");
            }
        }
        if ($error) {
            $this->container->getResponse()->redirect("admin/post/modify/$originalPostSlug");
        }

        //Update the post
        $postUpdate = $this->postModel->modifyPost($postId, $title, $postImage, $idCategory, $article, $published,
            $onFrontPage, $postSlug);

        // Tags
        //remove all tags
        $this->tagModel->removeTagsOnPost($postId);
        //set new tags
        if (isset($posts["tags"])) {
            $this->addTags($posts["tags"], $postId);
        }

        //checking result and redirecting
        if ($postUpdate) {
            $this->alertBox->setAlert("Post " . $title . " Updated");
            $this->container->getResponse()->redirect("admin/post/modify/" . $postSlug);
        }
        $this->alertBox->setAlert("Error updating " . $title, "error");
        $this->container->getResponse()->redirect("admin/post/modify/" . $originalPostSlug);
    }


    /**
     * deletes a specific post
     * @param int $postId
     * @throws \Exception
     */
    public function deletePost(int $postId)
    {
        $postTitle = $this->postModel->getTitleFromId($postId);
        //first remove tags or foreign key error
        $this->tagModel->removeTagsOnPost($postId);
        $removedPost = $this->postModel->deletePost($postId);

        if($removedPost)
        {
            $this->alertBox->setAlert("Post ".$postTitle." deleted");
        }

        $this->response->redirect("admin/post/list/");
    }
}