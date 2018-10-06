<?php

namespace App\Controllers\Admin;

use App\Models\TagModel;
use Core\AdminController;
use Core\Constant;
use Core\Container;

class Tag extends AdminController{

    protected $siteConfig;
    protected $pagination;

    private $tagModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        $this->loadModules[] = 'pagination';
        parent::__construct($container);

        $this->tagModel = new TagModel($this->container);

        $this->data['configs'] = $this->siteConfig->getSiteConfig();
    }

    public function list(string $page = "page-1", int $linesPerPage = Constant::LIST_PER_PAGE)
    {
        $this->onlyAdmin();

        $totalCategories = $this->tagModel->countTags();
        $pagination = $this->pagination->getPagination($page, $totalCategories, $linesPerPage);

        if($linesPerPage !== Constant::LIST_PER_PAGE){
            $this->data['paginationPostsPerPage'] = $linesPerPage;
        }

        $this->data["posts"] = $this->tagModel->getTagList($pagination["offset"], $linesPerPage);
        $this->data['pagination'] = $pagination;
        $this->renderView("Admin/ListTag");
    }
}