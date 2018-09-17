<?php
namespace App\Models;

use Core\Container;
use Core\Model;

class TagsModel extends Model{

    private $tagAssoTbl;
    private $tagTbl;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->tagAssoTbl = $this->getTablePrefix("posts_has_tags");
        $this->tagTbl = $this->getTablePrefix("tags");
    }

    private function postHasTag(int $postId, int $tagId):bool
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
    private function getTagId(string $tagName):int
    {
        $sql = "SELECT idtags FROM $this->tagTbl WHERE tag_name = :tagName";
        $this->query($sql);
        $this->bind(':tagName', $tagName);
        $this->execute();
        //if no rows, return zero
        if(!$this->stmt->rowCount() > 0){
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
    private function createNewTag(string $tag):int
    {
        $sql = "INSERT INTO $this->tagTbl (tag_name) VALUES (:tag)";
        $this->query($sql);
        $this->bind(":tag", $tag);
        $this->execute();
        return $this->dbh->lastInsertId();

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
        if($this->postHasTag($postId, $tagId)){
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
        if($tagId === 0){
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
        if(!$this->postHasTag($postId, $tagId)){
            return;
        }

        $sql = "DELETE FROM $this->tagAssoTbl WHERE post_idposts = :postId AND tag_idtags = :tagId";
        $this->query($sql);
        $this->bind(':postId', $postId);
        $this->bind(':tagId', $tagId);
        $this->execute();
    }


}