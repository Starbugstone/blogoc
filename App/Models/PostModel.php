<?php

namespace App\Models;

use Core\Constant;
use Core\Container;
use Core\Model;
use Core\Traits\StringFunctions;

class PostModel extends Model
{
    use StringFunctions;

    private $postsTbl;
    private $categoriesTbl;
    private $usersTbl;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->postsTbl = $this->getTablePrefix('posts');
        $this->categoriesTbl = $this->getTablePrefix('categories');
        $this->usersTbl = $this->getTablePrefix('users');
    }

    /**
     * get all the posts with details
     * @param int $offset where to start (for pagination)
     * @param int $limit the number of posts
     * @param bool $isFrontPage extract only front page posts
     * @return array list of posts
     * @throws \ErrorException
     */
    private function getAllPosts(int $offset, int $limit, bool $isFrontPage = false)
    {
        $sql = "SELECT title, post_image,article,$this->postsTbl.last_update, posts_slug, category_name, categories_slug, pseudo as author, idusers
                FROM $this->postsTbl INNER JOIN $this->categoriesTbl ON $this->postsTbl.categories_idcategories = $this->categoriesTbl.idcategories
                INNER JOIN $this->usersTbl ON $this->postsTbl.author_iduser = $this->usersTbl.idusers";
        if ($isFrontPage) {
            $sql .= " WHERE on_front_page = 1";
        }
        $sql .= " ORDER BY $this->postsTbl.creation_date DESC";
        $sql .= " LIMIT $limit OFFSET $offset";
        $this->query($sql);
        $this->execute();
        $results = $this->fetchAll();
        $sendResults = [];
        //we create the excerpt for the text and add it to the object
        foreach ($results as $result) {
            $result->{'excerpt'} = $this->getExcerpt($result->article);
            $sendResults[] = $result;
        }
        return $sendResults;
    }

    /**
     * get the list of front posts
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws \ErrorException
     */
    public function getFrontPosts(int $offset = 0, int $limit = Constant::FRONT_PAGE_POSTS)
    {
        return $this->getAllPosts($offset, $limit, true);
    }

    /**
     * get the list of all the posts.
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws \ErrorException
     */
    public function getPosts(int $offset = 0, int $limit = Constant::POSTS_PER_PAGE)
    {
        return $this->getAllPosts($offset, $limit, false);
    }


    /**
     * Create a new post
     * @param string $title
     * @param string $postImage
     * @param int $idCategory
     * @param string $article
     * @param int $idUser
     * @param int $published
     * @param int $onFrontPage
     * @param string $postSlug
     * @return string
     * @throws \Exception
     */
    public function newPost(
        string $title,
        string $postImage,
        int $idCategory,
        string $article,
        int $idUser,
        int $published,
        int $onFrontPage,
        string $postSlug
    ) {
        $sql = "
          INSERT INTO $this->postsTbl (title, post_image, categories_idcategories, article, author_iduser, creation_date, published, on_front_page, posts_slug)
          VALUES (:title, :post_image, :categories_idcategories, :article, :author_iduser, NOW(), :published, :on_front_page, :posts_slug)
        ";
        $this->query($sql);
        $this->bind(':title', $title);
        $this->bind(':post_image', $postImage);
        $this->bind(':categories_idcategories', $idCategory);
        $this->bind(':article', $article);
        $this->bind(':author_iduser', $idUser);
        $this->bind(':published', $published);
        $this->bind(':on_front_page', $onFrontPage);
        $this->bind(':posts_slug', $postSlug);

        $this->execute();

        return $this->dbh->lastInsertId();

    }

    /**
     * get all the posts from a certain category
     * @param int $categoryId the id of the category
     * @return array list of posts in set category
     * @throws \Exception
     */
    public function getPostsInCategory(int $categoryId): array
    {
        $sql = "SELECT * FROM $this->postsTbl WHERE categories_idcategories = :categoryId;";
        $this->query($sql);
        $this->bind(":categoryId", $categoryId, \PDO::PARAM_INT);
        $this->execute();

        return $this->fetchAll();
    }

}