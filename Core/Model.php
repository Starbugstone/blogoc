<?php
namespace Core;
use App\Config;
use PDO;

abstract class Model{
    private $host = Config::DB_HOST;
    private $db   = Config::DB_NAME;
    private $user = Config::DB_USER;
    private $pass = Config::DB_PASSWORD;
    private $charset = 'utf8';

    protected $dbh; //database handler

    protected $stmt; //statement

    protected $error; //for the errors if needed

    public function __construct()
    {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset"; //Creating the Data Source name
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        $this->dbh = new PDO($dsn, $this->user, $this->pass, $opt);
    }
}