<?php

namespace Core;

use App\Config;
use PDO;

/**
 * Class Model here we have all the generic calls to be inherited by the App\Models
 * using PDO connections
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

    /**
     * Model constructor.
     * creating the database connection on construct
     */
    public function __construct()
    {
            $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset"; //Creating the Data Source name
            $opt = [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $opt);
    }

    /*
     * generic PDO query constructor
     * ---------------------------------------------
     */

    /**
     * creating and storing the query
     * @param $sql string creating the sql query
     */
    protected function query($sql):void{

        $this->stmt = $this->dbh->prepare($sql);
    }

    /**
     * @param $param
     * @param $value
     * @param null $type
     * @throws \Exception error if no sql query to bind to
     */
    protected function bind($param, $value, $type = null):void{
        if($this->stmt != null){
            if (is_null($type)){ //need a bind value, so just check it in code. that way we can just call bind(param,value)
                switch(true){
                    case is_int($value):
                        $type = PDO::PARAM_INT;
                        break;
                    case is_bool($value):
                        $type = PDO::PARAM_BOOL;
                        break;
                    case is_null($value):
                        $type = PDO::PARAM_NULL;
                        break;
                    default:
                        $type = PDO::PARAM_STR;
                }
            }
            $this->stmt->bindValue($param, $value, $type);
        }else{
            throw new \Exception("No query to bind to");
        }

    }

    protected function execute(){
        if($this->stmt != null){
            return $this->stmt->execute();
        }else{
            throw new \Exception("No statement to execute");
        }

    }

    /*
     * END generic PDO query constructor
     * ---------------------------------------------
     */

    /**
     * correlation between the model name and the table name
     *if we don't have a table name, get the table that has the same name as the model will be returned (else, we do nothing !!)
     * Also search if the table exists, if not do a check in the views (must be v_$table)
     * @param string|null $table the name of the table to get, if none the get the table of the models name
     * @return string the table name (with an s)
     * @throws \ReflectionException the model doesn't exist, should never happen
     * @throws \Exception table or view doesn't exist
     */
    private function getTable(string $table=null){
        if($table == null){
            $reflect = new \ReflectionClass(get_class($this));
            $table = $reflect->getShortName(); //this is to only get the model name, otherwise we get the full namespace
            $table = $table.'s';
        }

        //IF table exists
            //SQL search for tables : show full tables where Table_Type = 'BASE TABLE'
            //or show full tables where Table_Type != 'VIEW'
            //$result = mysql_query("SHOW TABLES LIKE 'myTable'");
            //$tableExists = mysql_num_rows($result) > 0;
            //TODO Testing with Db needed
            //--------------
            //return table
        //ELSEIF v_table exists
            //return view
        //ELSE
            //throw error table or view doesn't exist
        return $table;
    }

    protected function test(){
        echo '<p>'.get_class($this).' <- Our Class</p>';

        echo '<p>'.$this->getTable().' <- Our shortend Class</p>';
    }
}