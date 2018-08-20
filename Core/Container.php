<?php

namespace Core;

use Core\Dependency\Request;
use Core\Dependency\Response;
use Core\Dependency\Session;
use PDO;


/**
 * Class Container for dependency injection
 * we take care of setting our template and database connections
 * We also call our Request and Session objects for the SuperGlobals access
 * @package Core
 *
 * PHP version 7
 */
class Container
{

    //used for the model connection
    /**
     * @var null this is to store the pdo connection. We only need to set once
     */
    private $dbh = null;

    /**
     * @var Dependency\Request object
     */
    private $request;

    /**
     * @var Dependency\Session object
     */
    private $session;


    private $response;

    /**
     * gets the twig template environment
     * @return \Twig_Environment
     */
    public function getTemplate(): \Twig_Environment
    {
        $twigOptions = [];
        if (!Config::DEV_ENVIRONMENT) {
            $twigOptions = [
                'cache' => dirname(__DIR__) . '/Cache'
            ];
        }
        $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views');
        $twig = new \Twig_Environment($loader, $twigOptions);

        return $twig;
    }

    /**
     * create the database connection via PDO
     * @return PDO
     */
    public function setPdo(): \PDO
    {
        if ($this->dbh) {
            return $this->dbh;
        }
        $dsn = "mysql:host=" . Config::DB_HOST . ";dbname=" . Config::DB_NAME . ";charset=utf8"; //Creating the Data Source name
        $opt = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        $this->dbh = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD, $opt);;
        return $this->dbh;
    }


    /**
     * Creates the request object if not already present and returns it
     * @return Dependency\Request|Request
     */
    public function getRequest(): Dependency\Request
    {
        if (!$this->request) {
            $this->request = new Request();
        }
        return $this->request;
    }

    /**
     * Creates the response object if not already present and returns it
     * @return Response
     */
    public function getResponse(): Dependency\Response
    {
        if (!$this->response) {
            $this->response = new Response();
        }
        return $this->response;
    }

    /**
     * Creates the session object if not already present and returns it
     * @return Dependency\Session|session
     */
    public function getSession(): Dependency\Session
    {
        if (!$this->session) {
            $this->session = new Session();
        }
        return $this->session;
    }

}