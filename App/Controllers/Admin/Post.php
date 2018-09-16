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
        //TODO have we receved a $_POST, if yes then probably an error on the create new post
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

    }

    /**
     * Shows the post to modify and update
     * @param $idPost
     */
    public function modify($idPost)
    {
        $this->onlyAdmin();

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

        //TODO
        //Slug, check if duplicate
        //Tags, check if duplicate before creating new tag
        //Tags, must have created the post and got the id before associating the tags
        //grab author from session

        $title = $posts["newPostTitle"];
        $postImage = $posts["newPostImage"]; //TODO Sanatize the input ? Or will PDO be enough ?
        $postSlug = $posts["newPostSlug"]; //TODO Check if unique
        $article = $posts["newPostTextArea"];
        $idCategory = $posts["categorySelector"];
        $published = $posts["isPublished"];
        $onFrontpage = $posts["isOnFrontPage"];
        $idUser = $userSessionid;

        $slugModel = new SlugModel($this->container);
        if (!$slugModel->isUnique($postSlug, "posts", "posts_slug")) {
            die("SLUG not unique");
        }

        $postModel = new PostModel($this->container);

        $postId = $postModel->newPost($title, $postImage, $idCategory, $article, $idUser, $published, $onFrontpage,
            $postSlug);

        echo "<p>new post ID : " . $postId . "</p>";

        //TODO add the tags and create new tags if necessary

        echo "<pre>";
        var_dump($posts);
        die();

        //TODO send a return $_POST with all data to the new post page if errors (also need to check if new page recives post, then add the data back into the forms

        //TODO else redirect to the modify page on OK ? good usability or stay on the new page with blank ?
    }
}