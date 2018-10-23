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

    public function loadComments()
    {
        $commentOffset = (int)$this->request->getData("commentOffset");
        $postId = (int)$this->request->getData("postId");

        $result = array();
        $result["success"]=false;
        $data = array();

        $data["comments"] = $this->commentModel->getCommentsListOnPost($postId, $commentOffset);
        if($data["comments"] !== false)
        {
            $result["success"]=true;
            $twig = $this->container->getTemplate();
            $html = $twig->render('Includes/LoadComments.twig', $data);

            $result["html"] = $html;
            $result["commentOffset"] = $commentOffset + count($data["comments"]);
        }


        echo json_encode($result);
    }

}