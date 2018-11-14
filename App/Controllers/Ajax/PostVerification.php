<?php

namespace App\Controllers\Ajax;

use App\Models\PostModel;
use Core\AjaxController;
use Core\Traits\StringFunctions;
use Core\Container;

class PostVerification extends AjaxController
{
    use StringFunctions;


    protected $slug;

    private $postModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'Slug';
        parent::__construct($container);
        $this->postModel = new PostModel($container);
    }

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
        $postId = (int)$this->request->getData("postId");

        $data = false;
        if (!$this->slug->isSlugValid($postSlug)) {
            echo json_encode($data);
            die();
        }

        $data = $this->postModel->isPostSlugUnique(/** @scrutinizer ignore-type */$postSlug); //we have checked that slug is valid so no type error

        if ($data === false) //slug is not unique, but could be from the same post
        {
            $slugOfId = $this->postModel->getPostSlugFromId($postId);
            if ($slugOfId === $postSlug) {
                //it's the same post, return true
                $data = true;
            }
        }
        echo json_encode($data);
    }

}