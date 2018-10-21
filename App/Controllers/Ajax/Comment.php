<?php

namespace App\Controllers\Ajax;

use App\Models\CommentModel;
use Core\AjaxController;
use Core\Container;

class Comment extends AjaxController
{
    private $commentModel;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->commentModel = new CommentModel($this->container);
    }

    /**
     * Update the approved status of a comment
     * @throws \Core\JsonException
     */
    public function modifyApproved()
    {
        $this->onlyAdmin();
        $this->onlyPost();
        $state = (bool)($this->request->getData("state") === 'true');
        $commentId = (int)$this->request->getData("commentId");

        $result = array();
        $result["success"] = $this->commentModel->setApproved(!$state, $commentId);
        $result["state"] = !$state;
        $result["commentId"] = $commentId;
        echo json_encode($result);
    }

}