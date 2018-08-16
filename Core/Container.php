<?php
namespace Core;

use PDO;


class Container{

    //used for the model connection
    private $dbh = null;


    public function getTemplate()
    {
        $twigOptions = [];
        if(!Config::DEV_ENVIRONMENT){
            $twigOptions = [
              'cache' =>   dirname(__DIR__).'/Cache'
            ];
        }
        $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views');
        $twig = new \Twig_Environment($loader, $twigOptions); //need to add cache

        return $twig;
    }

    //only set once
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