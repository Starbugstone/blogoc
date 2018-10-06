<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;
use App\Models\SlugModel;
use Core\AdminController;
use Core\Constant;
use Core\Container;

class Category extends AdminController{

    protected $siteConfig;
    protected $pagination;

    private $categoryModel;
    private $slugModel;


    public function __construct(Container $container)
    {
        $this->loadModules[] = 'SiteConfig';
        $this->loadModules[] = 'pagination';
        parent::__construct($container);

        $this->categoryModel = new CategoryModel($this->container);
        $this->slugModel = new SlugModel($this->container);

        $this->data['configs'] = $this->siteConfig->getSiteConfig();
        $this->data['categories'] = $this->categoryModel->getCategories();
    }

    /**
     * Listing all the categories
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


        $totalCategories = $this->categoryModel->countCategories();
        $pagination = $this->pagination->getPagination($page, $totalCategories, $linesPerPage);

        if($linesPerPage !== Constant::LIST_PER_PAGE){
            $this->data['paginationPostsPerPage'] = $linesPerPage;
        }

        $this->data["posts"] = $this->categoryModel->getCategoryList($pagination["offset"], $linesPerPage);
        $this->data['pagination'] = $pagination;
        $this->renderView("Admin/ListCategory");
    }

    /**
     * Post function to update a category
     * @throws \ErrorException
     */
    public function update()
    {
        $this->onlyAdmin();
        if (!$this->request->isPost()) {
            $this->alertBox->setAlert('Only post messages allowed', 'error');
            $this->response->redirect('admin');
        }

        $category = $this->container->getRequest()->getDataFull();

        $categoryId = $category["idcategories"];
        $categoryName = $category["category_name"];
        $categorySlug = $category["categories_slug"];

        //Sanity check on ID
        if($categoryId == null )
        {
            throw new \ErrorException("invalid category ID");
        }

        $originalCategorySlug = $this->slugModel->getSlugFromId($categoryId, "categories", "idcategories","categories_slug");

        //Error checking
        $error = false;
        if($categoryName == "")
        {
            $error = true;
            $this->alertBox->setAlert("empty name not allowed", "error");
        }
        if($categorySlug == "")
        {
            $error = true;
            $this->alertBox->setAlert("empty slug not allowed", "error");
        }
        if (!$this->slugModel->isUnique($categorySlug, "categories", "categories_slug") && $categorySlug !== $originalCategorySlug) {
            $error = true;
            $this->alertBox->setAlert("Slug not unique", "error");
        }

        if ($error) {
            $this->container->getResponse()->redirect("/admin/category/list");
        }

        $categoryUpdate = $this->categoryModel->update($categoryId, $categoryName, $categorySlug);

        //checking result and redirecting
        if ($categoryUpdate) {
            $this->alertBox->setAlert("Category " . $categoryName . " updated");
            $this->container->getResponse()->redirect("/admin/category/list/");
        }
        $this->alertBox->setAlert("Error updating " . $categoryName, "error");
        $this->container->getResponse()->redirect("/admin/category/list/");
    }

    /**
     * Delete a specific category
     * @param int $categoryId
     * @throws \Exception
     */
    public function delete(int $categoryId)
    {
        $this->onlyAdmin();
        $categoryName = $this->categoryModel->getNameFromId($categoryId);

        $removedCategory = $this->categoryModel->delete($categoryId);

        if($removedCategory)
        {
            $this->alertBox->setAlert("Category ".$categoryName." deleted");
        }

        $this->response->redirect("/admin/category/list/");

    }

    /**
     * create a new category
     */
    public function new()
    {
        $this->onlyAdmin();
        if (!$this->request->isPost()) {
            $this->alertBox->setAlert('Only post messages allowed', 'error');
            $this->response->redirect('admin');
        }

        $category = $this->container->getRequest()->getDataFull();
        $categoryName = $category["category_name"];
        $categorySlug = $category["categories_slug"];

        //Error checking
        $error = false;
        if($categoryName == "")
        {
            $error = true;
            $this->alertBox->setAlert("empty name not allowed", "error");
        }
        if($categorySlug == "")
        {
            $error = true;
            $this->alertBox->setAlert("empty slug not allowed", "error");
        }
        if (!$this->slugModel->isUnique($categorySlug, "categories", "categories_slug")) {
            $error = true;
            $this->alertBox->setAlert("Slug not unique", "error");
        }

        if ($error) {
            $this->container->getResponse()->redirect("/admin/category/list");
        }

        $categoryNew = $this->categoryModel->new($categoryName, $categorySlug);

        //checking result and redirecting
        if ($categoryNew) {
            $this->alertBox->setAlert("Category " . $categoryName . " created");
            $this->container->getResponse()->redirect("/admin/category/list/");
        }
        $this->alertBox->setAlert("Error creating " . $categoryName, "error");
        $this->container->getResponse()->redirect("/admin/category/list/");
    }
}