<?php

namespace Core;

use App\Config;
use PDO;

/**
 * Class Model here we have all the generic calls to be inherited by the App\Models
 * @package Core
 */
abstract class Model
{
    /**
     * @var string $host stores the DataBase Host grabbed from the config
     */
    private $host = Config::DB_HOST;
    /**
     * @var string $db stores the DataBase Name grabbed from the config
     */
    private $db = Config::DB_NAME;
    /**
     * @var string $user stores the DataBase User Name grabbed from the config
     */
    private $user = Config::DB_USER;
    /**
     * @var string $pass stores the DataBase User password grabbed from the config
     */
    private $pass = Config::DB_PASSWORD;

    /**
     * @var string $charset forcing the charset to UTF8
     */
    private $charset = 'utf8';

    protected $dbh; //database handler

    protected $stmt; //statement

    protected $error; //for the errors if needed

    public function __construct()
    {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset"; //Creating the Data Source name
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        $this->dbh = new PDO($dsn, $this->user, $this->pass, $opt);
    }
}