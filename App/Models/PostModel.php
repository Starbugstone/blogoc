<?php

namespace App\Models;

use Core\Constant;
use Core\Model;
use Core\Traits\StringFunctions;

class PostModel extends Model
{
    use StringFunctions;

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
        $postsTbl = $this->getTablePrefix('posts');
        $categoriesTbl = $this->getTablePrefix('categories');
        $usersTbl = $this->getTablePrefix('users');

        $sql = "SELECT title, post_image,article,$postsTbl.last_update, posts_slug, category_name, categories_slug, pseudo as author, idusers
                FROM $postsTbl INNER JOIN $categoriesTbl ON $postsTbl.categories_idcategories = $categoriesTbl.idcategories
                INNER JOIN $usersTbl ON $postsTbl.author_iduser = $usersTbl.idusers";
        if ($isFrontPage) {
            $sql .= " WHERE on_front_page = 1";
        }
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

        $postsTbl = $this->getTablePrefix('posts');
        $sql = "
          INSERT INTO $postsTbl (title, post_image, categories_idcategories, article, author_iduser, creation_date, published, on_front_page, posts_slug)
          VALUES (:title, :post_image, :categories_idcategories, :article, :author_iduser, NOW(), :published, :on_front_page, :posts_slug)
        ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':post_image', $postImage);
        $stmt->bindValue(':categories_idcategories', $idCategory);
        $stmt->bindValue(':article', $article);
        $stmt->bindValue(':author_iduser', $idUser);
        $stmt->bindValue(':published', $published);
        $stmt->bindValue(':on_front_page', $onFrontPage);
        $stmt->bindValue(':posts_slug', $postSlug);

        $stmt->execute();

        return $this->dbh->lastInsertId();

    }

}