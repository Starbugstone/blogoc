<?php

namespace App\Controllers\Admin;

use App\Models\CommentModel;
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
    }


    public function viewComments(string $page = "page-1", int $linesPerPage = Constant::LIST_PER_PAGE)
    {
        $this->onlyAdmin();

        $totalComments = $this->commentModel->countPendingComments();
        $pagination = $this->pagination->getPagination($page, $totalComments, $linesPerPage);

        if ($linesPerPage !== Constant::LIST_PER_PAGE) {
            $this->data['paginationPostsPerPage'] = $linesPerPage;
        }

        $this->data['pagination'] = $pagination;
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data["comments"] = $this->commentModel->getPendingCommentsList($pagination["offset"], $linesPerPage);


        $this->renderView('Admin/Comments');
    }

    public function pendingComments(string $page = "page-1", int $linesPerPage = Constant::LIST_PER_PAGE)
    {
        $this->onlyAdmin();

        $totalComments = $this->commentModel->countPendingComments();
        $pagination = $this->pagination->getPagination($page, $totalComments, $linesPerPage);

        if ($linesPerPage !== Constant::LIST_PER_PAGE) {
            $this->data['paginationPostsPerPage'] = $linesPerPage;
        }

        $this->data['pagination'] = $pagination;
        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data["comments"] = $this->commentModel->getPendingCommentsList($pagination["offset"], $linesPerPage);
        $this->data['pendingView'] = true;

        $this->renderView('Admin/Comments');

    }

    public function moderateComment(int $commentId)
    {
        $this->onlyAdmin();


        $this->renderView('Admin/ViewComment');
    }
}