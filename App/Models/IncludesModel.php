<?php

namespace App\Models;

use Core\Model;

/**
 * Class IncludesModel
 * All the included views data (menu, jumbotron, ...)
 * the returned data should then be stored in a $data['var'] and the view will check if var is set
 * @package App\Models
 */
class IncludesModel extends Model
{
    /**
     * get all the menu elements from the database
     * @return array the categories and access URL
     * @throws \ReflectionException
     */
    public function getMenu(): array
    {
        $data = [];
        //get the categories from database
        $categories = $this->getResultSet('categories');
        foreach ($categories as $category) {
            $data += [
                $category->category_name => '/category/' . $category->categories_slug
            ];
        }
        return $data;
    }
}