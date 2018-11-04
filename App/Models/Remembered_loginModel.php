<?php

namespace App\Models;

use Core\Constant;
use Core\Container;
use Core\Model;

class Remembered_loginModel extends Model
{

    private $token;
    private $rememberedLoginTbl;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->rememberedLoginTbl = $this->getTablePrefix("remembered_logins");
    }

    /**
     *
     * @param string|null $token_value
     * @throws \Exception
     */
    public function setToken(string $token_value = null)
    {
        if ($token_value) {
            $this->token = $token_value;
        } else {
            $this->token = $this->generateToken();
        }
    }

    /**
     * get the generated token
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * get the token hash from a user id
     * @param int $userId
     * @return mixed
     * @throws \Exception
     */
    public function getTokenHashFromId(int $userId)
    {
        $sql = "
            SELECT token_hash FROM $this->rememberedLoginTbl
            WHERE users_idusers = :userId
        ";
        $this->query($sql);
        $this->bind(':userId', $userId);
        $this->execute();
        return $this->stmt->fetchColumn();
    }

    /**
     * get the hash of the token
     * @return string
     */
    public function getHash()
    {
        return $this->generateHash($this->token);
    }

    /**
     * Find a user session token in the database
     * @param string $token
     * @return mixed
     * @throws \Exception
     */
    public function findByToken(string $token)
    {
        $this->setToken($token);
        $hashedToken = $this->getHash();

        $sql = "
            SELECT * FROM $this->rememberedLoginTbl
            WHERE token_hash = :hashedToken
        ";
        $this->query($sql);
        $this->bind(':hashedToken', $hashedToken);
        $this->execute();
        $result = $this->fetch();

        if ($result) {
            if (strtotime($result->expires_at) < time()) {
                //token has expired
                $this->deleteToken($hashedToken);
                return false;
            }
        }

        return $result;

    }

    /**
     * Add a remember me token we store the token and use the hash in the database
     * @param int $userId
     * @return bool
     * @throws \Exception
     */
    public function rememberMe(int $userId): \stdClass
    {
        $result = new \stdClass();
        $result->token = $this->getToken();
        $tokenHash = $this->getHash();
        $result->expiry_timestamp = time() + 60 * 60 * 24 * 30; //expires in 30 days

        $sql = "
            INSERT INTO $this->rememberedLoginTbl (token_hash, users_idusers, expires_at)
            VALUES (:hashedToken, :userId, :expiresAt)
        ";
        $this->query($sql);
        $this->bind(':hashedToken', $tokenHash);
        $this->bind(':userId', $userId);
        $this->bind(':expiresAt', date('Y-m-d H:i:s', $result->expiry_timestamp));
        $result->success = $this->execute();
        return $result;

    }

    /**
     * delete a token from database
     * @param $tokenHash
     * @throws \Exception
     */
    public function deleteToken($tokenHash)
    {
        $sql = "
            DELETE FROM $this->rememberedLoginTbl
            WHERE token_hash = :hashedToken;
        ";
        $this->query($sql);
        $this->bind(':hashedToken', $tokenHash);
        $this->execute();
    }

    /**
     * removes old tokens from database
     * @throws \Exception
     */
    public function cleanUpTokens()
    {
        $sql = "
            DELETE FROM $this->rememberedLoginTbl
            WHERE expires_at < :time
        ";
        $this->query($sql);
        $this->bind(':time', time());
        $this->execute();
    }
}