<?php

namespace App\Controllers\Ajax;

use App\Models\SlugModel;
use Core\AjaxController;
use Core\Traits\StringFunctions;

class PostVerification extends AjaxController
{
    use StringFunctions;

    /**
     * checks if the slug is unique
     * @return bool is unique
     * @throws \Core\JsonException
     * @throws \ErrorException
     */
    public function isSlugUnique()
    {
        $this->onlyAdmin();
        if (!$this->container->getRequest()->isPost()) {
            throw new \Core\JsonException('Call is not post');
        }

        $postSlug = $this->request->getData("postSlug");
        $postId = $this->request->getData("postId");

        $data = false;
        if(!$this->isAlphaNum($postSlug))
        {
            echo json_encode($data);
            return;
        }

        $slugModel = new SlugModel($this->container);

        $data = $slugModel->isUnique($postSlug, "posts", "posts_slug");

        if($data == false) //slug is not unique, but could be from the same post
        {
            $slugOfId = $slugModel->getSlugFromId($postId, "posts", "idposts","posts_slug");
            if($slugOfId === $postSlug)
            {
                //it's the same post, return true
                $data = true;
            }
        }



        echo json_encode($data);
    }

}