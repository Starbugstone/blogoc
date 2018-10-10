<?php

namespace App\Models;

use Core\Container;
use Core\Model;

class UserModel extends Model
{

    private $userTbl;
    private $roleTbl;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->userTbl = $this->getTablePrefix("users");
        $this->roleTbl = $this->getTablePrefix("roles");
    }

    /**
     * Get all the useful data about a user from his ID
     * @param int $userId
     * @return mixed
     * @throws \Exception
     */
    public function getUserDetailsById(int $userId)
    {
        $sql = "
            SELECT idusers, username, avatar, email, surname, name, creation_date, last_update, locked_out, role_name, role_level
            FROM $this->userTbl
            INNER JOIN $this->roleTbl ON $this->userTbl.roles_idroles = $this->roleTbl.idroles
            WHERE idusers = :userId
        ";
        $this->query($sql);
        $this->bind(':userId', $userId);
        $this->execute();
        return $this->fetch();
    }

    /**
     * Get all the useful data about a user from his mail
     * @param string $email
     * @return mixed
     * @throws \Exception
     */
    public function getUserDetailsByEmail(string $email)
    {
        //check if email is valid for sanity
        if (!filter_var($this->user->email, FILTER_VALIDATE_EMAIL))
        {
            $email = htmlspecialchars($email);
            throw new \Exception("invalid email ".$email);
        }
        $sql = "
            SELECT idusers, username, avatar, email, surname, name, creation_date, last_update, locked_out, role_name, role_level
            FROM $this->userTbl
            INNER JOIN $this->roleTbl ON $this->userTbl.roles_idroles = $this->roleTbl.idroles
            WHERE email = :email
        ";
        $this->query($sql);
        $this->bind(':email', $email);
        $this->execute();
        return $this->fetch();
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

    public function registerUser(\stdClass $userData): int
    {

        //TODO need to get the default user role. Config ??
        $passwordHash = password_hash($userData->password, PASSWORD_DEFAULT);

        $sql = "
            INSERT INTO $this->userTbl (username, email, password, surname, name, creation_date, last_update, roles_idroles, locked_out, bad_login_tries)
            VALUES (:username, :email, :password, :surname, :name, NOW(), NOW(), :roles_idroles, 0, 0)
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