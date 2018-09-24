<?php

namespace App\Models;

use Core\Model;

class CategoryModel extends Model
{

    /**
     * get the list of categories and return all the data
     * @return array the categories
     * @throws \ReflectionException
     */
    public function getCategories()
    {
        return $this->getResultSet('categories');
    }

    /**
     * get all the menu elements from the database
     * @return array the categories and access URL
     * @throws \ReflectionException
     */
    public function getMenu(): array
    {
        $data = [];
        //get the categories from database
        $categories = $this->getCategories();
        foreach ($categories as $category) {
            $data += [
                $category->category_name => '/category/posts/' . $category->categories_slug
            ];
        }
        return $data;
    }
}