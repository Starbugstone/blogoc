<?php

namespace App\Models;

use Core\Constant;
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
        return $this->count('categories');
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
        return $this->list($offset, $limit, 'categories');
    }

    /**
     * Update a specific category
     * @param int $categoryId
     * @param string $categoryName
     * @param string $categorySlug
     * @return bool
     * @throws \Exception
     */
    public function update(int $categoryId, string $categoryName, string $categorySlug):bool
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
    public function new(string $categoryName, string $categorySlug):bool
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
    public function delete(int $categoryId):bool
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

    /**
     * get the category slug from the ID
     * @param int $categoryId
     * @return string
     * @throws \ReflectionException
     */
    public function getCategorySlugFromId(int $categoryId): string
    {
        return $this->getSlugFromId($categoryId, "idcategories", "categories_slug", "categories");
    }

    /**
     * check if category slug is unique
     * @param string $categorySlug
     * @return bool
     * @throws \Exception
     */
    public function isCategorySlugUnique(string $categorySlug):bool
    {
        return $this->isSlugUnique($categorySlug, "categories_slug", "categories");
    }

    /**
     * Get the category ID from a slug
     * @param string $categorySlug
     * @return int
     * @throws \Exception
     */
    public function getCategoryIdFromSlug(string $categorySlug): int
    {
        return $this->getIdFromSlug($categorySlug, "idcategories", "categories_slug", "categories");
    }


}