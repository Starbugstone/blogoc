<?php

namespace App\Controllers\Ajax;

use App\Models\CategoryModel;
use Core\AjaxController;
use Core\Container;
use Core\Traits\StringFunctions;

class Category extends AjaxController
{
    use StringFunctions;

    protected $slug;

    private $categoryModel;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'Slug';
        parent::__construct($container);

        $this->categoryModel = new CategoryModel($this->container);
    }

    /**
     * Create a new category via Ajax
     * @throws \Core\JsonException
     */
    public function new()
    {
        //security checks
        $this->onlyAdmin();
        $this->onlyPost();

        //preparing our return results
        $result = array();
        $categoryUpdateJson = ($this->request->getData('category-new'));
        $categoryUpdate = json_decode($categoryUpdateJson);

        //Converting our array of objects to simple array
        $send = array();
        foreach ($categoryUpdate as $item) {
            $send[$item->name] = $item->value;
        }

        if (!$this->slug->isSlugValid($send["categories_slug"])) {
            $result["success"] = false;
            $result["errorMessage"] = "Invalid Slug";
            echo json_encode($result);
            die();
        }


        $result["success"] = $this->categoryModel->new($send["category_name"], $send["categories_slug"]);
        echo json_encode($result);
    }

    /**
     * Update the category via ajax
     * @throws \Core\JsonException
     */
    public function update()
    {
        //security checks
        $this->onlyAdmin();
        $this->onlyPost();

        //preparing our return results
        $result = array();
        $categoryUpdateJson = ($this->request->getData('category-update'));
        $categoryUpdate = json_decode($categoryUpdateJson);

        //Converting our array of objects to simple array
        $send = array();
        foreach ($categoryUpdate as $item) {
            $send[$item->name] = $item->value;
        }
        if (!$this->slug->isSlugValid($send["categories_slug"])) {
            $result["success"] = false;
            $result["errorMessage"] = "Invalid Slug";
            echo json_encode($result);
            die();
        }

        if (!$this->isInt($send["idcategories"])) {
            $result["success"] = false;
            $result["errorMessage"] = "Invalid ID";
            echo json_encode($result);
            die();
        }

        $result['success'] = $this->categoryModel->update($send["idcategories"], $send["category_name"],
            $send["categories_slug"]);
        echo json_encode($result);
    }

    /**
     * Delete a category via Ajax
     * @throws \Core\JsonException
     */
    public function delete()
    {
        //security checks
        $this->onlyAdmin();
        $this->onlyPost();

        //preparing our return results
        $result = array();
        $categoryDeleteJson = ($this->request->getData('category-delete'));
        $categoryDelete = json_decode($categoryDeleteJson);

        //Converting our array of objects to simple array
        $send = array();
        foreach ($categoryDelete as $item) {
            $send[$item->name] = $item->value;
        }

        if (!$this->isInt($send["idcategories"])) {
            $result["success"] = false;
            $result["errorMessage"] = "Invalid ID";
            echo json_encode($result);
            die();
        }

        $result['success'] = $this->categoryModel->delete($send["idcategories"]);
        echo json_encode($result);
    }
}