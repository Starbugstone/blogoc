<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;
use App\Models\PostModel;
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

    }

    /**
     * Shows the post to modify and update
     * @param $idPost
     */
    public function modify($idPost)
    {
        $this->onlyAdmin();

    }

    public function createNewPost()
    {
        //Security checks
        $this->onlyAdmin();
        if (!$this->request->isPost()) {
            $this->alertBox->setAlert('Only post messages allowed', 'error');
            $this->response->redirect('admin');
        }
        $posts = $this->container->getRequest()->getDataFull();

        //TODO
        //Slug, check if duplicate
        //Tags, check if duplicate before creating new tag
        //Tags, must have created the post and got the id before associating the tags
        //grab author from session

        $title = $posts["newPostTitle"];
        $postImage = "http://lorempixel.com/400/200/"; //TODO Change this, need image upload
        $postSlug = $posts["newPostSlug"]; //TODO Check if unique
        $article = $posts["newPostTextArea"];
        $idCategory = $posts["categorySelector"];
        $published = $posts["isPublished"];
        $onFrontpage = $posts["isOnFrontPage"];
        $idUser = 1; //TODO Get from session

        $postModel = new PostModel($this->container);

        $postId = $postModel->newPost($title,$postImage,$idCategory,$article,$idUser,$published,$onFrontpage,$postSlug);

        echo"<p>new post ID : ".$postId."</p>";

        echo "<pre>";
        var_dump($posts);
        die();
    }
}