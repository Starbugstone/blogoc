<?php

namespace App\Controllers\Ajax;

use App\Models\PostModel;
use Core\AjaxController;
use Core\Traits\StringFunctions;

class PostVerification extends AjaxController
{
    use StringFunctions;

    /**
     * checks if the slug is unique
     * @return bool is unique
     * @throws \Core\JsonException
     * @throws \Exception
     */
    public function isSlugUnique()
    {
        $this->onlyAdmin();
        $this->onlyPost();

        $postSlug = $this->request->getData("postSlug");
        $postId = $this->request->getData("postId");

        $data = false;
        if (!$this->isAlphaNum($postSlug) || $this->isInt($postId)) {
            echo json_encode($data);
            die();
        }

        $postModel = new PostModel($this->container);

        $data = $postModel->isPostSlugUnique($postSlug);

        if ($data === false) //slug is not unique, but could be from the same post
        {
            $slugOfId = $postModel->getPostSlugFromId($postId);
            if ($slugOfId === $postSlug) {
                //it's the same post, return true
                $data = true;
            }
        }
        echo json_encode($data);
    }

}