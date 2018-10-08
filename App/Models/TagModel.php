<?php

namespace App\Models;

use Core\Container;
use Core\Model;

class TagModel extends Model
{

    private $tagAssoTbl;
    private $tagTbl;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->tagAssoTbl = $this->getTablePrefix("posts_has_tags");
        $this->tagTbl = $this->getTablePrefix("tags");
    }

    /**
     * Counts the number of tags in DB
     * @return int
     * @throws \Exception
     */
    public function countTags(): int
    {
        return $this->count();
    }

    /**
     * get the list of tags with pagination limit and offset
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function getTagList(int $offset = 0, int $limit = Constant::POSTS_PER_PAGE)
    {
        return $this->list($offset, $limit);
    }

    /**
     * check if post has a specific tag
     * @param int $postId
     * @param int $tagId
     * @return bool
     * @throws \Exception
     */
    private function postHasTag(int $postId, int $tagId): bool
    {

        $sql = "SELECT * FROM $this->tagAssoTbl WHERE post_idposts = :postId AND tag_idtags = :tagId";
        $this->query($sql);
        $this->bind(':postId', $postId);
        $this->bind(':tagId', $tagId);
        $this->execute();

        return $this->stmt->rowCount() > 0;
    }

    /**
     * @param string $tagName the tag to search for
     * @return int
     * @throws \Exception
     */
    private function getTagId(string $tagName): int
    {
        $sql = "SELECT idtags FROM $this->tagTbl WHERE tag_name = :tagName";
        $this->query($sql);
        $this->bind(':tagName', $tagName);
        $this->execute();
        //if no rows, return zero
        if (!$this->stmt->rowCount() > 0) {
            return 0;
        }
        return $this->stmt->fetchColumn();
    }

    /**
     * Create a new tag and return it's ID
     * @param string $tag tag to insert
     * @return int the inserted tag ID
     * @throws \Exception
     */
    private function createNewTag(string $tag): int
    {
        $sql = "INSERT INTO $this->tagTbl (tag_name) VALUES (:tag)";
        $this->query($sql);
        $this->bind(":tag", $tag);
        $this->execute();
        return (int)$this->dbh->lastInsertId();

    }

    /**
     * delete a tag from all posts
     * @param int $tagId
     * @return bool
     * @throws \Exception
     */
    private function deleteTagOnAllPosts(int $tagId)
    {
        $sql = "
        DELETE 
        FROM $this->tagAssoTbl 
        WHERE tag_idtags = :tagId
        ";
        $this->query($sql);
        $this->bind(":tagId", $tagId);
        return $this->execute();
    }

    /**
     * @return array the list of all the tags
     * @throws \ReflectionException
     */
    public function getTags(): array
    {
        return $this->getResultSet('tags');
    }

    /**
     * Add a tag to the post
     * @param int $postId the post id
     * @param int $tagId the tag id
     * @throws \Exception
     */
    public function addTagToPost(int $postId, int $tagId)
    {
        //if the post already has the tag, do nothing
        if ($this->postHasTag($postId, $tagId)) {
            return;
        }

        $sql = "INSERT INTO $this->tagAssoTbl (post_idposts, tag_idtags) VALUES (:postId, :tagId)";
        $this->query($sql);
        $this->bind(':postId', $postId);
        $this->bind(':tagId', $tagId);
        $this->execute();
    }

    /**
     * Add a new tag to a post
     * @param int $postId
     * @param string $tag
     * @throws \Exception
     */
    public function addNewTagToPost(int $postId, string $tag)
    {
        //check if tag doesn't already exist
        $tagId = $this->getTagId($tag);
        if ($tagId === 0) {
            $tagId = $this->createNewTag($tag);
        }
        $this->addTagToPost($postId, $tagId);
    }

    /**
     * removes a tag from the post
     * @param int $postId the post id
     * @param int $tagId the tag id
     * @throws \Exception
     */
    public function removeTagFromPost(int $postId, int $tagId)
    {
        //if the tag isn't present, do nothing
        if (!$this->postHasTag($postId, $tagId)) {
            return;
        }

        $sql = "DELETE FROM $this->tagAssoTbl WHERE post_idposts = :postId AND tag_idtags = :tagId";
        $this->query($sql);
        $this->bind(':postId', $postId);
        $this->bind(':tagId', $tagId);
        $this->execute();
    }

    /**
     * get all tags associated to a post
     * @param int $postId the post ID
     * @return array the associated tags
     * @throws \Exception
     */
    public function getTagsOnPost(int $postId)
    {
        $sql = "SELECT tag_name, idtags FROM $this->tagTbl 
        INNER JOIN $this->tagAssoTbl ON  $this->tagTbl.idtags = $this->tagAssoTbl.tag_idtags
        WHERE post_idposts = :postId
        ";
        $this->query($sql);
        $this->bind(":postId", $postId);
        $this->execute();

        return $this->fetchAll();
    }


    /**
     * Remove all tags from a post
     * @param int $postId the post ID
     * @throws \Exception
     */
    public function removeTagsOnPost(int $postId)
    {
        $sql = "
            DELETE FROM $this->tagAssoTbl
            WHERE post_idposts = :postId
        ;";
        $this->query($sql);
        $this->bind(":postId", $postId);
        $this->execute();
    }

    /**
     * return all the tag details
     * @param int $tagId
     * @return array
     * @throws \ReflectionException
     */
    public function getTagDetails(int $tagId)
    {
        return $this->getRowById($tagId);
    }

    /**
     * create a new tag
     * @param string $tag
     * @return bool
     * @throws \Exception
     */
    public function new(string $tag)
    {
        $tagId = $this->createNewTag($tag);
        return is_int($tagId);
    }

    /**
     * Update an existing tag
     * @param int $tagId
     * @param string $tagName
     * @return bool
     * @throws \Exception
     */
    public function update(int $tagId, string $tagName)
    {
        $sql = "
            UPDATE $this->tagTbl 
            SET
              tag_name = :tagName
            WHERE
              idtags = :tagId
        ";
        $this->query($sql);
        $this->bind(":tagName", $tagName);
        $this->bind(":tagId", $tagId);
        return $this->execute();
    }

    /**
     * Delete a tag
     * @param int $tagId
     * @return bool
     * @throws \Exception
     */
    public function delete(int $tagId)
    {
        $this->deleteTagOnAllPosts($tagId);
        $sql = "
        DELETE
        FROM $this->tagTbl
        WHERE idtags = :tagId
        ";
        $this->query($sql);
        $this->bind(":tagId", $tagId);
        return $this->execute();
    }

    /**
     * get tag name from ID
     * @param int $tagId
     * @return mixed
     * @throws \Exception
     */
    public function getNameFromId(int $tagId)
    {
        $sql = "SELECT tag_name from $this->tagTbl WHERE idtags = :tagId";
        $this->query($sql);
        $this->bind(":tagId", $tagId);
        $this->execute();
        return $this->stmt->fetchColumn();
    }

}