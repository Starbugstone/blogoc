<?php

namespace App\Models;

use Core\Model;
use Core\Container;
use Core\Constant;

class CommentModel extends Model{

    private $commentTbl;
    private $userTbl;
    private $postTbl;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->commentTbl = $this->getTablePrefix("comments");
        $this->userTbl = $this->getTablePrefix("users");
        $this->postTbl = $this->getTablePrefix("posts");
    }

    private function baseSql()
    {
        $sql = "
            SELECT idcomments, users_idusers, posts_idposts, comment, approved, idposts, title, posts_slug, idusers, username
            FROM $this->commentTbl 
            LEFT JOIN $this->postTbl ON $this->commentTbl.posts_idposts = $this->postTbl.idposts
            LEFT JOIN $this->userTbl ON $this->commentTbl.users_idusers = $this->userTbl.idusers
        ";
        return $sql;
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
        $sql = $this->baseSql();
        $sql .= "
            WHERE approved = 1
            AND posts_idposts = :postId
            LIMIT :limit OFFSET :offset
        ";

        $this->query($sql);
        $this->bind(":limit", $limit);
        $this->bind(":offset", $offset);
        $this->bind(":postId", $postId);
        $this->execute();
        return $this->fetchAll();
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
        $sql = $this->baseSql();
        $sql .= "
          WHERE approved = 0
          LIMIT :limit OFFSET :offset
        ";
        $this->query($sql);
        $this->bind(":limit", $limit);
        $this->bind(":offset", $offset);
        $this->execute();

        return $this->fetchAll();
    }

    public function countComments(): int
    {
        return $this->count($this->commentTbl);
    }


    public function getCommentsList(int $offset = 0, int $limit = Constant::POSTS_PER_PAGE)
    {
        $sql = $this->baseSql();
        $sql .= "
          LIMIT :limit OFFSET :offset
        ";
        $this->query($sql);
        $this->bind(":limit", $limit);
        $this->bind(":offset", $offset);
        $this->execute();

        return $this->fetchAll();
    }

    /**
     * Add a comment to a post
     * @param int $postId
     * @param int $userId
     * @param string $message
     * @return int
     * @throws \Exception
     */
    public function addComment(int $postId, int $userId, string $message):int
    {
        $sql="
            INSERT INTO $this->commentTbl (users_idusers, posts_idposts, comment, approved)
            VALUES (:userId, :postId, :comment, 0)
        ";
        $this->query($sql);
        $this->bind(':userId', $userId);
        $this->bind(':postId', $postId);
        $this->bind(':comment', $message);

        $this->execute();
        return (int)$this->dbh->lastInsertId();
    }

    /**
     * delete a comment by it's ID
     * @param int $commentId
     * @return bool
     * @throws \Exception
     */
    public function delete(int $commentId)
    {
        $sql = "
        DELETE FROM $this->commentTbl 
        WHERE idcomments = :commentId
        ";
        $this->query($sql);
        $this->bind(":commentId", $commentId);
        return $this->execute();
    }

    /**
     * get a comment from it's ID
     * @param int $commentId
     * @return mixed
     * @throws \Exception
     */
    public function getCommentById(int $commentId)
    {
        $sql = $this->baseSql();
        $sql .= "
          WHERE idcomments = :commentId
        ";
        $this->query($sql);
        $this->bind(':commentId', $commentId);
        $this->execute();

        return $this->fetch();
    }

    /**
     * Set the approved state
     * @param bool $state
     * @param int $commentId
     * @return bool
     * @throws \Exception
     */
    public function setApproved(bool $state, int $commentId):bool
    {
        $sql = "
            UPDATE $this->commentTbl 
            SET
              approved = :state
            WHERE
              idcomments = :commentId
        ";
        $this->query($sql);
        $this->bind(":commentId", $commentId);
        $this->bind(":state", $state);
        return $this->execute();
    }

}