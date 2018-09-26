<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\SlugModel;
use App\Models\TagsModel;
use Core\AdminController;

class Post extends AdminController
{

    /**
     * page for new post
     */
    public function new()
    {
        $this->onlyAdmin();
        $categoryModel = new CategoryModel($this->container);
        $tagModel = new TagsModel($this->container);
        $this->data['categories'] = $categoryModel->getCategories();
        $this->data['tags'] = $tagModel->getTags();
        $this->renderView('Admin/NewPost');
    }

    /**
     * Lists all the posts
     */
    public function list()
    {
        $this->onlyAdmin();

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

        $categoryModel = new CategoryModel($this->container);
        $tagModel = new TagsModel($this->container);
        $postModel = new PostModel($this->container);
        $slugModel = new SlugModel($this->container);

        $postId = $slugModel->getIdFromSlug($slug, "posts", "posts_slug", "idposts");

        $this->data['post'] = $postModel->getSinglePost($postId);
        $this->data['postTags'] = $tagModel->getTagsOnPost($postId);
        $this->data['categories'] = $categoryModel->getCategories();
        $this->data['tags'] = $tagModel->getTags();
        $this->renderView('Admin/ModifyPost');
    }

    /**
     * Create a new post
     * @throws \ErrorException
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
        $userSessionid = $this->container->getSession()->get("user_id");


        $title = trim($posts["newPostTitle"]);
        $postImage = $posts["newPostImage"]; //TODO Sanatize the input ? Or will PDO be enough ?
        $postSlug = trim($posts["newPostSlug"]);
        $article = $posts["newPostTextArea"];
        $idCategory = $posts["categorySelector"];
        $published = $posts["isPublished"];
        $onFrontpage = $posts["isOnFrontPage"];
        $idUser = $userSessionid;

        if(!is_int($idUser) || $idUser === null)
        {
            throw new \Error("Invalid userID");
        }

        $slugModel = new SlugModel($this->container);
        $tagModel = new TagsModel($this->container);
        $postModel = new PostModel($this->container);

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
        if (!$slugModel->isUnique($postSlug, "posts", "posts_slug")) {
            $error = true;
            $this->alertBox->setAlert("Slug not unique", "error");
        }

        if ($error) {
            $this->container->getResponse()->redirect("admin/post/new");
        }

        $postId = $postModel->newPost($title, $postImage, $idCategory, $article, $idUser, $published, $onFrontpage,
            $postSlug);

        if (isset($posts["tags"])) {
            foreach ($posts["tags"] as $tag) {
                if (isset($tag["id"])) {
                    $tagModel->addTagToPost($postId, $tag["id"]);
                    continue;
                }
                $tagModel->addNewTagToPost($postId, $tag["name"]);
            }
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
        $onFrontpage = $posts["isOnFrontPage"];

        $slugModel = new SlugModel($this->container);
        $tagModel = new TagsModel($this->container);
        $postModel = new PostModel($this->container);

        //security and error checks
        $originalPostSlug = $slugModel->getSlugFromId($postId, "posts", "idposts",
            "posts_slug");
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
            if (!$slugModel->isUnique($postSlug, "posts", "posts_slug")) {
                $error = true;
                $originalPostSlug = $slugModel->getSlugFromId($postId, "posts", "idposts", "posts_slug");
                $this->alertBox->setAlert("Slug not unique", "error");
            }
        }
        if ($error) {
            $this->container->getResponse()->redirect("admin/post/modify/$originalPostSlug");
        }

        //Update the post
        $postUpdate = $postModel->modifyPost($postId, $title, $postImage, $idCategory, $article, $published,
            $onFrontpage, $postSlug);

        // Tags
        //remove all tags
        $tagModel->removeTagsOnPost($postId);
        //set new tags
        if (isset($posts["tags"])) {
            foreach ($posts["tags"] as $tag) {
                if (isset($tag["id"])) {
                    $tagModel->addTagToPost($postId, $tag["id"]);
                    continue;
                }
                $tagModel->addNewTagToPost($postId, $tag["name"]);
            }
        }

        //checking result and redirecting
        if ($postUpdate) {
            $this->alertBox->setAlert("Post " . $title . " Updated");
            $this->container->getResponse()->redirect("admin/post/modify/" . $postSlug);
        }
        $this->alertBox->setAlert("Error updating " . $title, "error");
        $this->container->getResponse()->redirect("admin/post/modify/" . $originalPostSlug);
    }
}