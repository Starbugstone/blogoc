<?php

namespace App\Models;

use App\Modules\Token;
use Core\BlogocException;
use Core\Constant;
use Core\Container;
use Core\Model;

class UserModel extends Model
{

    private $userTbl;
    private $roleTbl;

    /**
     * UserModel constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->userTbl = $this->getTablePrefix("users");
        $this->roleTbl = $this->getTablePrefix("roles");
    }

    /**
     * the basic select query. We can add restrictions on after in the functions.
     * @return string
     */
    private function baseSqlSelect(): string
    {
        $sql = "
            SELECT idusers, username, avatar, email, surname, name, creation_date, last_update, locked_out, bad_login_time, bad_login_tries, role_name, role_level, reset_password_hash, reset_password_hash_generation_datetime
            FROM $this->userTbl
            INNER JOIN $this->roleTbl ON $this->userTbl.roles_idroles = $this->roleTbl.idroles 
        ";
        return $sql;
    }

    /**
     * get the password from the user email. mainly for login purposes
     * @param string $email
     * @return string
     * @throws BlogocException
     */
    private function getUserPassword(string $email): string
    {
        if (!$this->isEmailUsed($email)) {
            throw new BlogocException("Email not present in Database");
        }
        $sql = "SELECT password FROM $this->userTbl WHERE email = :email";
        $this->query($sql);
        $this->bind(':email', $email);
        $this->execute();
        return $this->stmt->fetchColumn();
    }

    /**
     * called when authentication failed
     * @param $user
     * @throws \Exception
     */
    private function addToBadLoginTries($user): void
    {
        $badLoginTries = $user->bad_login_tries + 1;
        $sql = "
            UPDATE $this->userTbl
            SET
              bad_login_time = NOW(),
              bad_login_tries = :badLoginTries
            WHERE idusers = :userId
        ";
        $this->query($sql);
        $this->bind(':badLoginTries', $badLoginTries);
        $this->bind(':userId', $user->idusers);
        $this->execute();
    }

    /**
     * reset the bad login count
     * @param $user
     * @throws \Exception
     */
    private function resetBadLogin($user): void
    {
        $sql = "
            UPDATE $this->userTbl
            SET
              bad_login_tries = 0
            WHERE idusers = :userId
        ";
        $this->query($sql);
        $this->bind(':userId', $user->idusers);
        $this->execute();
    }

    private function isAccountPasswordBlocked($user)
    {
        if ($user->bad_login_tries < Constant::NUMBER_OF_BAD_PASSWORD_TRIES) {
            //not enough bad tries yet
            return false;
        }

        $blockTime = strtotime($user->bad_login_time);
        $currentTime = time();
        if ($currentTime - $blockTime > Constant::LOCKOUT_MINUTES * 60) {
            //we have outlived the timeout, connection authorised
            return false;
        }
        //the account is timed out
        return true;
    }

    /**
     * Get all the useful data about a user from his ID
     * @param int $userId
     * @return mixed
     * @throws \Exception
     */
    public function getUserDetailsById(int $userId)
    {
        $sql = $this->baseSqlSelect();
        $sql .= "
            WHERE idusers = :userId
        ";
        $this->query($sql);
        $this->bind(':userId', $userId);
        $this->execute();
        return $this->fetch();
    }

    /**
     * get the user details from a password reset token and Id (for security)
     * @param string $token
     * @param int $userId
     * @return mixed
     * @throws \Exception
     */
    public function getUserDetailsByToken(string $token, int $userId)
    {
        $hash = $this->generateHash($token);
        $sql = $this->baseSqlSelect();
        $sql .= "
            WHERE reset_password_hash = :token AND idusers = :userId
        ";
        $this->query($sql);
        $this->bind(':token', $hash);
        $this->bind(':userId', $userId);
        $this->execute();
        $user = $this->fetch();
        $linkValidTime = strtotime($user->reset_password_hash_generation_datetime);
        $currentTime = time();
        if ($currentTime - $linkValidTime > Constant::PASSWORD_RESET_DURATION * 60) {
            //token is no longer valid
            return false;
        }
        return $user;

    }

    /**
     * Get all the useful data about a user from his mail
     * @param string $email
     * @return mixed
     * @throws BlogocException
     */
    public function getUserDetailsByEmail(string $email)
    {
        //check if email is valid for sanity
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = htmlspecialchars($email);
            throw new BlogocException("invalid email " . $email);
        }
        $sql = $this->baseSqlSelect();
        $sql .= "
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
        return $this->getUserDetailsByEmail($email) !== false;
    }

    /**
     * register a new user
     * @param \stdClass $userData
     * @return int
     * @throws \Exception
     */
    public function registerUser(\stdClass $userData): int
    {
        $sql = "
            INSERT INTO $this->userTbl (username, email, surname, name, creation_date, last_update, roles_idroles, locked_out, bad_login_tries)
            VALUES (:username, :email, :surname, :name, NOW(), NOW(), :roles_idroles, 1, 0)
        ";
        $this->query($sql);
        $this->bind(':username', $userData->username);
        $this->bind(':email', $userData->email);
        $this->bind(':surname', $userData->surname);
        $this->bind(':name', $userData->name);
        $this->bind(':roles_idroles', 1); //we set to one, should probably get from database and config
        $this->execute();

        return (int)$this->dbh->lastInsertId();
    }

    /**
     * verify the user connection mail/password and login if ok
     * @param string $email
     * @param string $password
     * @return bool|mixed
     * @throws BlogocException
     */
    public function authenticateUser(string $email, string $password): \stdClass
    {
        $response = new \stdClass();
        $response->success = false;
        $response->message = "";

        $user = $this->getUserDetailsByEmail($email);

        if ($user === false) //no user exists
        {
            $response->message = "email doesn't exist, register a new account?";
            return $response;
        }

        //check if the user has validated his email
        if ($user->locked_out) {
            $response->message = "the email has not been verified, please check your inbox or click on 'reset your password'";
            return $response;
        }

        if ($this->isAccountPasswordBlocked($user)) {
            $response->message = "too many bad passwords, account is blocked for " . Constant::LOCKOUT_MINUTES . " minutes";
            return $response;
        }

        if (!password_verify($password, $this->getUserPassword($email))) {
            $response->message = "password is incorrect";
            $this->addToBadLoginTries($user);
            return $response;
        }


        //all ok, send user back for login
        $this->resetBadLogin($user);
        $response->user = $user;
        $response->success = true;
        return $response;
    }

    /**
     * generate a password hash for resetting or defining the password
     * @param int $userId
     * @return string the generated token
     * @throws \Exception
     */
    public function generatePasswordHash(int $userId): string
    {
        $user = $this->getUserDetailsById($userId);
        if (!$user) {
            //user Id doesn't exist, bail out
            throw new \Exception("User not found");
        }
        $token = $this->generateToken();
        $hash = $this->generateHash($token);

        $sql = "
            UPDATE $this->userTbl
            SET
              reset_password_hash = :hash,
              reset_password_hash_generation_datetime = NOW()
            WHERE idusers = :userId
        ";
        $this->query($sql);
        $this->bind(':hash', $hash);
        $this->bind(':userId', $user->idusers);
        $this->execute();

        return $token;
    }

    /**
     * Reset the user password
     * @param int $userId
     * @param string $password
     * @throws \Exception
     */
    public function resetPassword(int $userId, string $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "
            UPDATE $this->userTbl
            SET
              password = :password,
              locked_out = 0;
              last_update = NOW()
            WHERE idusers = :userId
        ";
        $this->query($sql);
        $this->bind(':password', $hash);
        $this->bind(':userId', $userId);
        $this->execute();
    }


}