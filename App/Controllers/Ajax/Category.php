<?php

namespace App\Controllers\Ajax;

use App\Models\CategoryModel;
use Core\AjaxController;

class Category extends AjaxController
{

    /**
     * Create a new category via Ajax
     * @throws \Core\JsonException
     */
    public function new()
    {
        //security checks
        $this->onlyAdmin();
        if (!$this->container->getRequest()->isPost()) {
            throw new JsonException('Call is not post');
        }

        //prepating our return results
        $result = array();
        $categoryUpdateJson = ($this->request->getData('category-new'));
        $categoryUpdate = json_decode($categoryUpdateJson);

        //Converting our array of objects to simple array
        $send = array();
        foreach ($categoryUpdate as $item) {
            $send[$item->name] = $item->value;
        }

        $categoryModel = new CategoryModel($this->container);
        $result['success'] = $categoryModel->new($send["category_name"], $send["categories_slug"]);
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
        if (!$this->container->getRequest()->isPost()) {
            throw new JsonException('Call is not post');
        }

        //prepating our return results
        $result = array();
        $categoryUpdateJson = ($this->request->getData('category-update'));
        $categoryUpdate = json_decode($categoryUpdateJson);

        //Converting our array of objects to simple array
        $send = array();
        foreach ($categoryUpdate as $item) {
            $send[$item->name] = $item->value;
        }

        $categoryModel = new CategoryModel($this->container);
        $result['success'] = $categoryModel->update($send["idcategories"], $send["category_name"],
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
        if (!$this->container->getRequest()->isPost()) {
            throw new JsonException('Call is not post');
        }

        //prepating our return results
        $result = array();
        $categoryDeleteJson = ($this->request->getData('category-delete'));
        $categoryDelete = json_decode($categoryDeleteJson);

        //Converting our array of objects to simple array
        $send = array();
        foreach ($categoryDelete as $item) {
            $send[$item->name] = $item->value;
        }

        $categoryModel = new CategoryModel($this->container);
        $result['success'] = $categoryModel->delete($send["idcategories"]);
        echo json_encode($result);
    }
}