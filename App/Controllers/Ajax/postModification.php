<?php

namespace App\Controllers\Ajax;

use App\Models\PostModel;
use Core\AjaxController;
use Core\Container;

class postModification extends AjaxController{


    private $postModule;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->postModule = new PostModel($this->container);
    }

    public function modifyPublished()
    {
        $this->onlyAdmin();
        if (!$this->container->getRequest()->isPost()) {
            throw new \Core\JsonException('Call is not post');
        }
        $state = (bool)($this->request->getData("state") === 'true');
        $postId = (int)$this->request->getData("postId");

        $result["success"] = $this->postModule->setPublished(!$state, $postId);
        $result["state"] = !$state;
        $result["postId"] = $postId;
        echo json_encode($result);
    }

    public function modifyOnFrontPage()
    {
        $this->onlyAdmin();
        if (!$this->container->getRequest()->isPost()) {
            throw new \Core\JsonException('Call is not post');
        }
        $state = ($this->request->getData("state") === 'true');
        $postId = (int)$this->request->getData("postId");

        $result["success"] = $this->postModule->setOnFrontPage(!$state, $postId);
        $result["state"] = !$state;
        $result["postId"] = $postId;
        echo json_encode($result);
    }
}