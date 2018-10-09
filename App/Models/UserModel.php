<?php

namespace App\Models;

use Core\Container;
use Core\Model;

class UserModel extends Model
{

    private $userTbl;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->userTbl = $this->getTablePrefix("users");
    }

    /**
     * Get all the data about a user for posts (Author).
     * @param int $authorId
     * @return array
     * @throws \ReflectionException
     */
    public function getAuthorDetails(int $authorId)
    {
        return $this->getRowById($authorId);
    }

    public function isEmailUnique(string $email)
    {
        $sql = "
            SELECT * FROM $this->userTbl WHERE email = :email
        ";
        $this->query($sql);
        $this->bind(':email', $email);
        $this->execute();

        return $this->stmt->rowCount() > 0;
    }
}