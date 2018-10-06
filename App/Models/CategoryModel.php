<?php

namespace App\Models;

use Core\Container;
use Core\Model;

class CategoryModel extends Model
{

    private $categoryTbl;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->categoryTbl = $this->getTablePrefix("categories");
    }

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

    /**
     * get all the details from the category table
     * @param int $categoryId
     * @return array
     * @throws \ReflectionException
     */
    public function getCategoryDetails(int $categoryId)
    {
        return $this->getRowById($categoryId, "categories");
    }

    /**
     * count the total number of categories
     * @return int
     * @throws \Exception
     */
    public function countCategories(): int
    {
        $sql = "SELECT COUNT(*) FROM $this->categoryTbl";
        $this->query($sql);
        $this->execute();
        return $this->stmt->fetchColumn();
    }

    /**
     * get the list of categories with pagination
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function getCategoryList(int $offset = 0, int $limit = Constant::POSTS_PER_PAGE)
    {
        $sql = "
            SELECT * FROM $this->categoryTbl 
            LIMIT :limit OFFSET :offset
        ";
        $this->query($sql);
        $this->bind(":limit", $limit);
        $this->bind(":offset", $offset);
        $this->execute();
        return $this->fetchAll();
    }

    /**
     * Update a specific category
     * @param int $categoryId
     * @param string $categoryName
     * @param string $categorySlug
     * @return bool
     * @throws \Exception
     */
    public function update(int $categoryId, string $categoryName, string $categorySlug)
    {
        $sql = "
            UPDATE $this->categoryTbl
            SET
              category_name = :categoryName,
              categories_slug = :categorySlug
            WHERE
              idcategories = :categoryId
        ";
        $this->query($sql);
        $this->bind(":categoryName", $categoryName);
        $this->bind(":categorySlug", $categorySlug);
        $this->bind(":categoryId", $categoryId);
        return $this->execute();
    }

    /**
     * Create a new category
     * @param string $categoryName
     * @param string $categorySlug
     * @return bool
     * @throws \Exception
     */
    public function new(string $categoryName, string $categorySlug)
    {
        $sql = "
            INSERT INTO $this->categoryTbl (category_name, categories_slug)
            VALUES (:categoryName, :categorySlug)
        ";

        $this->query($sql);
        $this->bind(":categoryName", $categoryName);
        $this->bind(":categorySlug", $categorySlug);
        return $this->execute();
    }

    /**
     * delete a category by Id
     * @param int $categoryId
     * @return bool
     * @throws \Exception
     */
    public function delete(int $categoryId)
    {
        $sql = "
        DELETE FROM $this->categoryTbl 
        WHERE idcategories = :categoryId
        ";
        $this->query($sql);
        $this->bind(":categoryId", $categoryId);
        return $this->execute();
    }


    /**
     * return the name of the category from the ID
     * @param int $categoryId
     * @return mixed
     * @throws \Exception
     */
    public function getNameFromId(int $categoryId)
    {
        $sql = "SELECT category_name from $this->categoryTbl WHERE idcategories = :categoryId";
        $this->query($sql);
        $this->bind(":categoryId", $categoryId);
        $this->execute();
        return $this->stmt->fetchColumn();
    }



}