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
     * the base Select SQL to get the information from the post table and joined tables
     * @return string
     */
    private function basePostSelect(): string
    {
        $sql = "SELECT idposts, title, post_image,article,$this->postsTbl.last_update, posts_slug, categories_idcategories, category_name, published, on_front_page, categories_slug, pseudo as author, idusers
                FROM $this->postsTbl INNER JOIN $this->categoriesTbl ON $this->postsTbl.categories_idcategories = $this->categoriesTbl.idcategories
                INNER JOIN $this->usersTbl ON $this->postsTbl.author_iduser = $this->usersTbl.idusers";
        return $sql;
    }

    /**
     * add the excerpt to a post list
     * @param array $posts
     * @return array
     * @throws \ErrorException
     */
    private function addExcerpt(array $posts): array
    {
        $sendResults = [];
        //we create the excerpt for the text and add it to the object
        foreach ($posts as $post) {
            $post->{'excerpt'} = $this->getExcerpt($post->article);
            $sendResults[] = $post;
        }
        return $sendResults;
    }

    /**
     * get all the posts with details. Only selecting posts that are published
     * @param int $offset where to start (for pagination)
     * @param int $limit the number of posts
     * @param bool $isFrontPage extract only front page posts
     * @return array list of posts
     * @throws \ErrorException
     */
    private function getAllPublishedPosts(int $offset, int $limit, bool $isFrontPage = false): array
    {
        $sql = $this->basePostSelect();
        $sql .= " WHERE published = 1";
        if ($isFrontPage) {
            $sql .= " AND on_front_page = 1";
        }
        $sql .= " ORDER BY $this->postsTbl.creation_date DESC";
        $sql .= " LIMIT :limit OFFSET :offset";
        $this->query($sql);
        $this->bind(":limit", $limit);
        $this->bind(":offset", $offset);
        $this->execute();
        $results = $this->fetchAll();
        return $this->addExcerpt($results);
    }

    /**
     * get the total number of posts, useful for pagination
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
     * get the total number of posts in a category, useful for pagination
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
     * get the list of front posts
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws \ErrorException
     */
    public function getFrontPosts(int $offset = 0, int $limit = Constant::FRONT_PAGE_POSTS): array
    {
        return $this->getAllPublishedPosts($offset, $limit, true);
    }

    /**
     * get the list of all the posts.
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws \ErrorException
     */
    public function getPosts(int $offset = 0, int $limit = Constant::POSTS_PER_PAGE): array
    {
        return $this->getAllPublishedPosts($offset, $limit, false);
    }

    /**
     * get all the posts from a certain category
     * @param int $categoryId the id of the category
     * @param int $offset the offset for pagination
     * @param int $limit the limit to display
     * @return array list of posts in set category
     * @throws \Exception
     */
    public function getPostsInCategory(int $categoryId, int $offset = 0, int $limit = Constant::POSTS_PER_PAGE): array
    {
        $sql = $this->basePostSelect();
        $sql .= " WHERE categories_idcategories = :categoryId 
                ORDER BY $this->postsTbl.creation_date DESC
                LIMIT :limit OFFSET :offset;";
        $this->query($sql);
        $this->bind(":categoryId", $categoryId, \PDO::PARAM_INT);
        $this->bind(":limit", $limit);
        $this->bind(":offset", $offset);
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
     * get a single post from it's ID
     * @param int $postid the post ID to get
     * @return array the single post details
     * @throws \Exception
     */
    public function getSinglePost(int $postid)
    {
        $sql = $this->basePostSelect();
        $sql .= " WHERE idposts = :postId;";
        $this->query($sql);
        $this->bind(":postId", $postid, \PDO::PARAM_INT);
        $this->execute();

        return $this->fetch();
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
     * @return int the id of created post
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
    ): int {
        $sql = "
          INSERT INTO $this->postsTbl (title, post_image, categories_idcategories, article, author_iduser, creation_date, last_update, published, on_front_page, posts_slug)
          VALUES (:title, :post_image, :categories_idcategories, :article, :author_iduser, NOW(), NOW(), :published, :on_front_page, :posts_slug)
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
     * Update a post with new values
     * @param int $postId
     * @param string $title
     * @param string $postImage
     * @param int $idCategory
     * @param string $article
     * @param int $published
     * @param int $onFrontPage
     * @param string $postSlug
     * @return bool success
     * @throws \Exception
     */
    public function modifyPost(
        int $postId,
        string $title,
        string $postImage,
        int $idCategory,
        string $article,
        int $published,
        int $onFrontPage,
        string $postSlug
    ): bool {
        $sql = "
            UPDATE $this->postsTbl 
            SET 
                title = :title,
                post_image = :postImage,
                categories_idcategories = :idCategory,
                article = :article,
                last_update = NOW(),
                published = :published,
                on_front_page = :onFrontPage,
                posts_slug = :postSlug
            WHERE
              idposts = :postId
        ;";
        $this->query($sql);
        $this->bind(":title", $title);
        $this->bind(":postImage", $postImage);
        $this->bind(":idCategory", $idCategory);
        $this->bind(":article", $article);
        $this->bind(":published", $published);
        $this->bind(":onFrontPage", $onFrontPage);
        $this->bind(":postSlug", $postSlug);
        $this->bind(":postId", $postId);

        return $this->execute();
    }


}