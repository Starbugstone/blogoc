<?php

namespace App\Models;

use Core\Model;
use Core\Container;

class PaginationModel extends Model{

    private $postsTbl;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->postsTbl = $this->getTablePrefix('posts');
    }


    /**
     * get the total number of posts
     * @return int
     * @throws \Exception
     */
    public function totalNumberPosts(): int
    {
        $sql = "SELECT COUNT(*) FROM $this->postsTbl WHERE published = 1";
        $this->query($sql);
        $this->execute();
        return $this->stmt->fetchColumn();
    }

    /**
     * get the total number of posts in a category
     * @param int $categoryId
     * @return int
     * @throws \Exception
     */
    public function totalNumberPostsInCategory(int $categoryId): int
    {
        $sql = "SELECT COUNT(*) FROM $this->postsTbl WHERE published = 1 AND categories_idcategories = :categoryId ";
        $this->query($sql);
        $this->bind(":categoryId", $categoryId, \PDO::PARAM_INT);
        $this->execute();
        return $this->stmt->fetchColumn();
    }

    /**
     * get the total number of posts in a category
     * @param int $authorid
     * @return int
     * @throws \Exception
     */
    public function totalNumberPostsByAuthor(int $authorid):int
    {
        $sql = "SELECT COUNT(*) FROM $this->postsTbl WHERE published = 1 AND author_iduser = :authorId ";
        $this->query($sql);
        $this->bind(":authorId", $authorid, \PDO::PARAM_INT);
        $this->execute();
        return $this->stmt->fetchColumn();
    }
}