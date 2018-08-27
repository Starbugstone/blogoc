<?php
namespace App\Controllers\Admin;

use Core\AdminController;

class Post extends AdminController{

    /**
     * page for new post
     */
    public function new(){
        $this->onlyAdmin();
        $this->renderView('Admin/NewPost');
    }

    /**
     * Lists all the posts
     */
    public function list(){
        $this->onlyAdmin();

    }

    /**
     * Shows the post to modify and update
     * @param $idPost
     */
    public function modify($idPost){
        $this->onlyAdmin();

    }
}