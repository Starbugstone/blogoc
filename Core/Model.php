<?php

namespace Core;

use Core\Traits\StringFunctions;
use PDO;

/**
 * Class Model here we have all the generic calls to be inherited by the App\Models
 * using PDO connections
 * @package Core
 *
 * PHP version 7
 */
abstract class Model
{
    use StringFunctions;
    /**
     * @var PDO the database handeler
     */
    protected $dbh;

    /**
     * @var \PDOStatement|boolean the prepared sql statement
     */
    protected $stmt;

    /**
     * @var Container the dependancy injector
     */
    private $container;

    /**
     * Model constructor. prepares the database connection
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->dbh = $this->container->setPdo();
    }

    /*
     * generic PDO query constructor
     * ---------------------------------------------
     */

    /**
     * creating and storing the query
     * @param $sql string creating the sql query
     */
    protected function query($sql): void
    {
        $this->stmt = $this->dbh->prepare($sql);
    }

    /**
     * binding the parameters to the query. Need the stmt to be declared before via query()
     * @param $param
     * @param $value
     * @param  $type
     * @throws \Exception error if no sql query to bind to
     */
    protected function bind($param, $value, $type = null): void
    {
        if ($this->stmt == null) {
            throw new \Exception("No query to bind to");
        }
        if (is_null($type)) { //need a bind value, so just check it in code. that way we can just call bind(param,value)
            switch (true) {
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
    }

    /**
     * Execute our constructed SQL statement
     * @return bool
     * @throws \Exception if the statement is empty
     */
    protected function execute()
    {
        if ($this->stmt == null) {
            throw new \Exception("No statement to execute");
        }
        return $this->stmt->execute();
    }

    /**
     * fetches the result from an executed query
     * @return array
     */
    protected function fetchAll(){
        return $this->stmt->fetchAll();
    }

    /**
     * returns a single line from the executed query
     * @return mixed
     */
    protected function fetch(){
        return $this->stmt->fetch();
    }

    /*
     * END generic PDO query constructor
     * ---------------------------------------------
     */

    /**
     * correlation between the model name and the table name
     * if we don't have a table name, get the table that has the same name as the model will be returned (else, we do nothing !!)
     * Also search if the table exists, if not do a check in the views (must be v_$table)
     * @param string|null $table the name of the table to get, if none the get the table of the models name
     * @return string the table name (with an s)
     * @throws \ReflectionException the model doesn't exist, should never happen
     * @throws \Exception table or view doesn't exist
     * @return string table or view name
     */
    protected function getTable(String $table = null): String
    {
        //If no table is passed, get the calling model name
        if ($table === null) {
            $reflect = new \ReflectionClass(get_class($this));
            $table = $reflect->getShortName(); //this is to only get the model name, otherwise we get the full namespace
            //since our models all end with Model, we should remove it.
            $table = $this->removeFromEnd($table, 'Model');
            $table = $table . 's'; //adding the s since the table should be plural. Might be some special case where the plural isn't just with an s
            $table = strtolower($table); //the database names are in lowercase
        }

        $table = $this->getTablePrefix($table);

        //see if table exists
        $sql = "SHOW TABLES LIKE :table";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindValue(':table', $table, PDO::PARAM_STR);
        $stmt->execute();
        $exists = $stmt->rowCount() > 0; //will return 1 if table exists or 0 if non existant

        if ($exists) {
            //the table exists
            return $table;
        }

        //if we are here, then table doesn't exist, check for view
        $view = 'v_' . $table;
        $stmt->bindValue(':table', $view, PDO::PARAM_STR);
        $stmt->execute();
        $exists = $stmt->rowCount() > 0; //will return 1 if table exists or 0 if non existant

        if ($exists) {
            //the view exists
            return $view;
        }

        //neither table or view exists
        //throw an error
        throw new \Exception("Table or view $table doesn't exist");
    }

    /**
     * This function adds the table prefix if set and returns the name
     * Use this if we are sure of the table name. Avoids the DB calls
     * @param $table string the table name
     * @return string
     */
    protected function getTablePrefix($table){
        if(Config::TABLE_PREFIX != '')
        {
            $table = Config::TABLE_PREFIX.'_'.$table;
        }
        return $table;
    }

    /**
     * checks if the result from a PDO query has any data.
     * If yes then we return the array
     * If debugging is enabled we throw an exception on no results
     * or we just return an empty array
     * @param mixed $result the PDO result of a query
     * @return array the result or empty
     * @throws \Exception if debugging is on and no result
     */
    private function returnArray($result): array
    {
        if ($result) {
            return $result;
        }
        if (Config::DEV_ENVIRONMENT) {
            throw new \Exception("No results in database");
        }
        return [];
    }

    /**
     * gets the entire table or view and returns the array
     * @param string $table the table to search in, if empty then get the table based on model name
     * @return array the results from database
     * @throws \ReflectionException
     */
    protected function getResultSet($table = ''): array
    {
        $tableName = $this->getTable($table);
        $sql = "SELECT * FROM $tableName"; //can not pass table name as :parameter. since we already have tested if the table exists, this var should be safe.
        $this->query($sql);
        $this->execute();
        $result = $this->stmt->fetchAll(); //returns an array or false if no results
        return $this->returnArray($result);
    }

    /**
     * gets the entire table or view and returns the array with a limit to the number of rows
     * @param string $table the table to search in, if empty then get the table based on model name
     * @param string $limit the limit of rows to return
     * @return array the results from database
     * @throws \ReflectionException
     */
    protected function getResultSetLimited($limit, $table = ''): array
    {
        $tableName = $this->getTable($table);
        $sql = "SELECT * FROM $tableName LIMIT :limit";
        $this->query($sql);
        $this->bind(':limit', $limit);
        $this->execute();
        $result = $this->stmt->fetchAll(); //returns an array or false if no results
        return $this->returnArray($result);
    }

    /**
     * get's the result of SELECT * FROM table where idtable=$id
     * @param int $rowId searched id
     * @param string $table the table to search, if blank then we get the table or view based on the model name
     * @return array result or empty array
     * @throws \ReflectionException (probably not, but will throw an exception if debugging is on and no results)
     */
    protected function getRowById($rowId, $table = ''): array
    {
        $tableName = $this->getTable($table);
        $idName = 'id' . $tableName;
        $sql = "SELECT * FROM $tableName WHERE $idName = :rowId";
        $this->query($sql);
        $this->bind(':rowId', $rowId);
        $this->execute();
        $result = $this->stmt->fetch();
        return $this->returnArray($result);
    }

    /**
     * gets the row from the query SELECT * FROM table WHERE $columnName = $Value
     * @param string $columnName the column to search in. Does a regex check for security
     * @param string $value the value to search for
     * @param string $table the table to search, if blank then we get the table or view based on the model name
     * @return array the results or empty
     * @throws \ReflectionException (probably not, but will throw an exception if debugging is on and no results)
     * @throws \Exception if the column name consists of other characters than lower case, numbers and underscore for security
     */
    protected function getRowByColumn(String $columnName, $value, $table = ''): array
    {
        $tableName = $this->getTable($table);
        $columnNameOk = preg_match("/^[a-z0-9_]+$/i", $columnName); //testing if column name only has lower case, numbers and underscore
        if (!$columnNameOk) {
            throw new \Exception("Syntax error : Column name \"$columnName\" is not legal");
        }
        $sql = "SELECT * FROM $tableName WHERE $columnName = :value";
        $this->query($sql);
        $this->bind(':value', $value);
        $this->execute();
        $result = $this->stmt->fetch();
        return $this->returnArray($result);
    }

    /**
     * get's the result of SELECT * FROM table where table_slug=$slug
     * @param string $slug the slug to look up
     * @param string $table the table to search, if blank then we get the table or view based on the model name
     * @return array result or empty array
     * @throws \ReflectionException (probably not, but will throw an exception if debugging is on and no results)
     */
    protected function getRowBySlug(String $slug, $table = ''): array
    {
        $tableName = $this->getTable($table);
        $slugName = $tableName.'_slug';
        $sql = "SELECT * FROM $tableName WHERE $slugName = :slug";
        $this->query($sql);
        $this->bind(':slug', $slug);
        $this->execute();
        $result = $this->stmt->fetch();
        return $this->returnArray($result);
    }
}