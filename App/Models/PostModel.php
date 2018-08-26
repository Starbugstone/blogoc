<?php

namespace App\Models;

use Core\Constant;
use Core\Model;
use Core\Traits\StringFunctions;

class PostModel extends Model
{
    use StringFunctions;

    private function getAllPosts(int $offset, int $limit, bool $isFrontPage = false){
        $postsTbl = $this->getTablePrefix('posts');
        $categoriesTbl = $this->getTablePrefix('categories');
        $usersTbl = $this->getTablePrefix('users');

        $sql = "SELECT title, post_image,article,$postsTbl.last_update, posts_slug, category_name, categories_slug, pseudo as author, idusers
                FROM $postsTbl INNER JOIN $categoriesTbl ON $postsTbl.categories_idcategories = $categoriesTbl.idcategories
                INNER JOIN $usersTbl ON $postsTbl.author_iduser = $usersTbl.idusers";
        if($isFrontPage){
            $sql .= " WHERE on_front_page = 1";
        }
        $sql .=" LIMIT $limit OFFSET $offset";
        $this->query($sql);
        $this->execute();
        $results = $this->fetchAll();
        $sendResults= [];
        //we create the excerpt for the text and add it to the object
        foreach ($results as $result){
            $result->{'excerpt'} = $this->getExcerpt($result->article);
            $sendResults[] = $result;
        }
        return $sendResults;
    }

    public function getFrontPosts(int $offset = 0, int $limit = Constant::FRONT_PAGE_POSTS)
    {
        return $this->getAllPosts($offset, $limit, true);
    }

    public function getPosts(int $offset = 0, int $limit = Constant::POSTS_PER_PAGE){
        return $this->getAllPosts($offset, $limit, false);
    }
}