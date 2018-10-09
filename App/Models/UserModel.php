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

    /**
     * check if the email is present in the database
     * @param string $email
     * @return bool
     * @throws \Exception
     */
    public function isEmailUsed(string $email)
    {
        $sql = "
            SELECT * FROM $this->userTbl WHERE email = :email
        ";
        $this->query($sql);
        $this->bind(':email', $email);
        $this->execute();

        return $this->stmt->rowCount() > 0;
    }

    public function registerUser(\stdClass $userData) : int
    {

        $passwordHash = password_hash($userData->password, PASSWORD_DEFAULT );

        $sql = "
            INSERT INTO $this->userTbl (username, email, password, surname, name, creation_date, last_update, roles_idroles, locked_out, bad_login_tries)
            VALUES (:username, :email, :password, :surname, :name, NOW(), NOW(), :roles_idroles, 1, 0)
        ";
        $this->query($sql);
        $this->bind(':username', $userData->username);
        $this->bind(':email', $userData->email);
        $this->bind(':password', $passwordHash);
        $this->bind(':surname', $userData->surname);
        $this->bind(':name', $userData->name);
        $this->bind(':roles_idroles', 1);
        $this->execute();

        return (int)$this->dbh->lastInsertId();

    }
}