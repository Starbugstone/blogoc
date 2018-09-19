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
    private function getAllPosts(int $offset, int $limit, bool $isFrontPage = false):array
    {
        $sql = "SELECT title, post_image,article,$this->postsTbl.last_update, posts_slug, category_name, categories_slug, pseudo as author, idusers
                FROM $this->postsTbl INNER JOIN $this->categoriesTbl ON $this->postsTbl.categories_idcategories = $this->categoriesTbl.idcategories
                INNER JOIN $this->usersTbl ON $this->postsTbl.author_iduser = $this->usersTbl.idusers";
        if ($isFrontPage) {
            $sql .= " WHERE on_front_page = 1";
        }
        $sql .= " ORDER BY $this->postsTbl.creation_date DESC";
        $sql .= " LIMIT :limit OFFSET :offset";
        $this->query($sql);
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


    public function modifyPost(
        int $postId,
        string $title,
        string $postImage,
        int $idCategory,
        string $article,
        int $idUser,
        int $published,
        int $onFrontPage,
        string $postSlug
    )
    {
        $sql="
            UPDATE $this->postsTbl 
            SET 
                title = :title,
                post_image = :postImage,
                categories_idcategories = :idCategory,
                article = :article,
                author_iduser = :idUser,
                last_update = NOW(),
                published = :published,
                on_front_page = :onFrontPage,
                posts_slug = :postSlug
            WHERE
              idposts = :postId
        ;";
        $this->query($sql);
        $this->bind(":title",$title);
        $this->bind(":postImage",$postImage);
        $this->bind(":idCategory",$idCategory);
        $this->bind(":article",$article);
        $this->bind(":idUser",$idUser);
        $this->bind(":published",$published);
        $this->bind(":onFrontPage",$onFrontPage);
        $this->bind(":postSlug",$postSlug);
        $this->bind(":postId",$postId);

        return $this->execute();
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
        $sql = "SELECT title, post_image,article,$this->postsTbl.last_update, posts_slug, category_name, categories_slug, pseudo as author, idusers
                FROM $this->postsTbl INNER JOIN $this->categoriesTbl ON $this->postsTbl.categories_idcategories = $this->categoriesTbl.idcategories
                INNER JOIN $this->usersTbl ON $this->postsTbl.author_iduser = $this->usersTbl.idusers
                WHERE categories_idcategories = :categoryId 
                ORDER BY $this->postsTbl.creation_date DESC
                LIMIT :limit OFFSET :offset
                ";
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
        $sql = "SELECT idposts, title, post_image,article,$this->postsTbl.last_update, posts_slug, categories_idcategories, category_name, published, on_front_page, categories_slug, pseudo as author, idusers
                FROM $this->postsTbl INNER JOIN $this->categoriesTbl ON $this->postsTbl.categories_idcategories = $this->categoriesTbl.idcategories
                INNER JOIN $this->usersTbl ON $this->postsTbl.author_iduser = $this->usersTbl.idusers
                WHERE idposts = :postId 
                ;";
        $this->query($sql);
        $this->bind(":postId", $postid, \PDO::PARAM_INT);
        $this->execute();

        return $this->fetch();
    }
}