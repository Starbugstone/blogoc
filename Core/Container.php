<?php
namespace Core;

use PDO;

/**
 * Class Container for dependency injection
 * @package Core
 *
 * PHP version 7
 */
class Container{

    //used for the model connection
    /**
     * @var null this is to store the pdo connection. We only need to set once
     */
    private $dbh = null;

    /**
     * gets the twig template environment
     * @return \Twig_Environment
     */
    public function getTemplate()
    {
        $twigOptions = [];
        if(!Config::DEV_ENVIRONMENT){
            $twigOptions = [
              'cache' =>   dirname(__DIR__).'/Cache'
            ];
        }
        $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views');
        $twig = new \Twig_Environment($loader, $twigOptions);

        return $twig;
    }

    /**
     * create the database connection via PDO
     * @return null|PDO
     */
    public function setPdo(){
        if ($this->dbh){
            return $this->dbh;
        }
        $dsn = "mysql:host=".Config::DB_HOST.";dbname=".Config::DB_NAME.";charset=utf8"; //Creating the Data Source name
        $opt = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        $this->dbh = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD, $opt);;
        return $this->dbh;
    }
}