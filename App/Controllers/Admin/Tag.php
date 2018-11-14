<?php

namespace App\Controllers\Admin;

use App\Models\CommentModel;
use App\Models\TagModel;
use Core\AdminController;
use Core\Constant;
use Core\Container;

class Tag extends AdminController
{

    protected $siteConfig;
    protected $pagination;

    private $tagModel;
    private $commentModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        $this->loadModules[] = 'pagination';
        parent::__construct($container);

        $this->tagModel = new TagModel($this->container);
        $this->commentModel = new CommentModel($container);

        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data["pendingCommentsCount"] = $this->commentModel->countPendingComments();
    }

    /**
     * List all the posts with pagination
     * @param string $page
     * @param int $linesPerPage
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function list(string $page = "page-1", int $linesPerPage = Constant::LIST_PER_PAGE)
    {
        $this->onlyAdmin();

        $totalCategories = $this->tagModel->countTags();
        $pagination = $this->pagination->getPagination($page, $totalCategories, $linesPerPage);

        if ($linesPerPage !== Constant::LIST_PER_PAGE) {
            $this->data['paginationPostsPerPage'] = $linesPerPage;
        }

        $this->data["posts"] = $this->tagModel->getTagList($pagination["offset"], $linesPerPage);
        $this->data['pagination'] = $pagination;
        $this->renderView("Admin/ListTag");
    }

    /**
     * Post function to update a tag
     * @throws \ErrorException
     */
    public function update()
    {
        $this->onlyAdmin();
        $this->onlyPost();

        $tag = $this->request->getDataFull();

        $tagId = $tag["idtags"];
        $tagName = $tag["tag_name"];


        //Sanity check on ID
        if ($tagId == null) {
            throw new \ErrorException("invalid tag ID");
        }

        //Error checking
        $error = false;
        if ($tagName == "") {
            $error = true;
            $this->alertBox->setAlert("empty name not allowed", "error");
        }

        if ($error) {
            $this->response->redirect("/admin/tag/list");
        }

        $tagUpdate = $this->tagModel->update($tagId, $tagName);

        //checking result and redirecting
        if ($tagUpdate) {
            $this->alertBox->setAlert("Tag " . $tagName . " updated");
            $this->response->redirect("/admin/tag/list/");
        }
        $this->alertBox->setAlert("Error updating " . $tagName, "error");
        $this->response->redirect("/admin/tag/list/");
    }

    /**
     * Delete a specific tag
     * @param int $tagId
     * @throws \Exception
     */
    public function delete(int $tagId)
    {
        $this->onlyAdmin();
        $tagName = $this->tagModel->getNameFromId($tagId);

        $removedTag = $this->tagModel->delete($tagId);

        if ($removedTag) {
            $this->alertBox->setAlert("Tag " . $tagName . " deleted");
        }

        $this->response->redirect("/admin/tag/list/");

    }

    /**
     * create a new tag
     */
    public function new()
    {
        $this->onlyAdmin();
        $this->onlyPost();

        $tag = $this->request->getDataFull();
        $tagName = $tag["tag_name"];

        //Error checking
        $error = false;
        if ($tagName == "") {
            $error = true;
            $this->alertBox->setAlert("empty name not allowed", "error");
        }

        if ($error) {
            $this->container->getResponse()->redirect("/admin/tag/list");
        }

        $tagNew = $this->tagModel->new($tagName);

        //checking result and redirecting
        if ($tagNew) {
            $this->alertBox->setAlert("Tag " . $tagName . " created");
            $this->response->redirect("/admin/tag/list/");
        }
        $this->alertBox->setAlert("Error creating " . $tagName, "error");
        $this->response->redirect("/admin/tag/list/");
    }
}