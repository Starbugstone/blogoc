<?php
namespace App\Controllers\Ajax;

use App\Models\SlugModel;
use Core\AjaxController;
class PostVerification extends AjaxController{

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
            throw new JsonException('Call is not post');
        }

        $postSlug = $this->request->getData("postSlug");

        $slugModel = new SlugModel($this->container);
        $data = $slugModel->isUnique($postSlug, "posts", "posts_slug");
        echo json_encode($data);
    }


}