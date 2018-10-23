<?php

namespace App\Controllers\Admin;

use App\Models\CommentModel;
use App\Models\UserModel;
use Core\AdminController;
use Core\Constant;
use Core\Container;
use Core\Traits\StringFunctions;

class Comments extends AdminController{

    use StringFunctions;

    protected $siteConfig;
    protected $pagination;

    private $commentModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        $this->loadModules[] = 'pagination';
        parent::__construct($container);
        $this->commentModel = new CommentModel($this->container);

        $this->data['configs'] = $this->siteConfig->getSiteConfig();
    }

    /**
     * View all the comments with pagination
     * @param string $page
     * @param int $linesPerPage
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function viewComments(string $page = "page-1", int $linesPerPage = Constant::LIST_PER_PAGE)
    {
        $this->onlyAdmin();

        $totalComments = $this->commentModel->countComments();
        $pagination = $this->pagination->getPagination($page, $totalComments, $linesPerPage);

        if ($linesPerPage !== Constant::LIST_PER_PAGE) {
            $this->data['paginationPostsPerPage'] = $linesPerPage;
        }

        $this->data['pagination'] = $pagination;
        $this->data["comments"] = $this->commentModel->getCommentsList($pagination["offset"], $linesPerPage);


        $this->renderView('Admin/Comments');
    }

    /**
     * View all the pending comments with pagination
     * @param string $page
     * @param int $linesPerPage
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function pendingComments(string $page = "page-1", int $linesPerPage = Constant::LIST_PER_PAGE)
    {
        $this->onlyAdmin();

        $totalComments = $this->commentModel->countPendingComments();
        $pagination = $this->pagination->getPagination($page, $totalComments, $linesPerPage);

        if ($linesPerPage !== Constant::LIST_PER_PAGE) {
            $this->data['paginationPostsPerPage'] = $linesPerPage;
        }

        $this->data['pagination'] = $pagination;
        $this->data["comments"] = $this->commentModel->getPendingCommentsList($pagination["offset"], $linesPerPage);
        $this->data['pendingView'] = true;

        $this->renderView('Admin/Comments');

    }

    /**
     * View the moderate comment page with a specific comment
     * @param int $commentId
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function moderateComment(int $commentId)
    {
        $this->onlyAdmin();

        $comment = $this->commentModel->getCommentById($commentId);

        $userModel = new UserModel($this->container);
        $user = $userModel->getUserDetailsById($comment->idusers);

        $this->data["comment"] = $comment;
        $this->data["commenter"] = $user;

        $this->renderView('Admin/ViewComment');
    }

    /**
     * Delete a comment
     * @param int $commentId
     * @throws \Exception
     */
    public function delete(int $commentId)
    {
        $this->onlyAdmin();
        $removedComment = $this->commentModel->delete($commentId);

        if ($removedComment) {
            $this->alertBox->setAlert("Comment  deleted");
        }

        $refererUrl = $this->request->getReferer();
        if($refererUrl === null) //referer can return null, set default
        {
            $refererUrl = "admin/comments/view-comments";
        }
        $baseUrl = $this->request->getBaseUrl();
        $redirectUrl = $this->removeFromBeginning($refererUrl, $baseUrl);

        $this->response->redirect($redirectUrl);

    }

    /**
     * Update a comment via post
     * @throws \ErrorException
     */
    public function update()
    {
        $this->onlyAdmin();
        $this->onlyPost();

        $comment = $this->container->getRequest()->getDataFull();

        //$this->debug->dump($comment);

        $commentId = $comment["idcomments"];
        //Sanity check on ID
        if (!$this->isInt($commentId)) {
            throw new \ErrorException("invalid comment ID");
        }

        //update comment
        if($this->commentModel->update($commentId, $comment["commentTextArea"], $comment["commentApproved"]))
        {
            $this->alertBox->setAlert("Comment updated");
        }

        $this->response->redirect("/admin/comments/moderate-comment/".$commentId);
    }
}