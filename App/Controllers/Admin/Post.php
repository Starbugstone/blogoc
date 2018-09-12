<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;
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

        echo "<pre>";
        var_dump($posts);
        die();
    }
}