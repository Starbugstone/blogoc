<?php

namespace App\Models;

use Core\Model;
use Core\Container;
use Core\Constant;

class CommentModel extends Model{

    private $commentTbl;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->commentTbl = $this->getTablePrefix("comments");
    }

    /**
     * Count the number of comments on a post
     * @param int $postId
     * @return int
     * @throws \Exception
     */
    public function countCommentsOnPost(int $postId): int
    {
        $sql = "SELECT COUNT(*) FROM $this->commentTbl WHERE posts_idposts = :postId";
        $this->query($sql);
        $this->bind(":postId", $postId);
        $this->execute();
        return $this->stmt->fetchColumn();
    }

    /**
     * the list of comments on a post with limit and offset
     * @param int $postId
     * @param int $offset
     * @param int $limit
     * @return bool
     * @throws \Exception
     */
    public function getCommentsListOnPost(int $postId, int $offset = 0, int $limit = Constant::COMMENTS_PER_PAGE)
    {
        $sql = "
            SELECT * FROM $this->commentTbl
            WHERE approved = 1
            AND posts_idposts = :postId
            LIMIT :limit OFFSET :offset
        ";

        $this->query($sql);
        $this->bind(":limit", $limit);
        $this->bind(":offset", $offset);
        $this->bind(":postId", $postId);
        return $this->execute();
    }

    /**
     * count the number of pending comments
     * @return mixed
     * @throws \Exception
     */
    public function countPendingComments()
    {
        $sql = "SELECT COUNT(*) FROM $this->commentTbl WHERE approved = 0";
        $this->query($sql);
        $this->execute();
        return $this->stmt->fetchColumn();
    }

    /**
     * get the list of pending comments with limit and offset
     * @param int $offset
     * @param int $limit
     * @return bool
     * @throws \Exception
     */
    public function getPendingCommentsList(int $offset = 0, int $limit = Constant::COMMENTS_PER_PAGE)
    {
        $sql = "
          SELECT * FROM $this->commentTbl 
          WHERE approved = 0
          LIMIT :limit OFFSET :offset
        ";
        $this->query($sql);
        $this->bind(":limit", $limit);
        $this->bind(":offset", $offset);
        return $this->execute();
    }

}