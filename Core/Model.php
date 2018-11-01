<?php

namespace Core;

use Core\Traits\StringFunctions;
use Exception;
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
     * @throws Exception error if no sql query to bind to
     */
    protected function bind($param, $value, $type = null): void
    {
        if ($this->stmt == null) {
            throw new Exception("No query to bind to");
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
     * @throws Exception if the statement is empty
     */
    protected function execute()
    {
        if ($this->stmt == null) {
            throw new Exception("No statement to execute");
        }
        return $this->stmt->execute();
    }

    /**
     * close the connection
     */
    protected function closeCursor()
    {
        $this->stmt->closeCursor();
    }

    /**
     * execute request then close
     * @return bool
     * @throws Exception
     */
    protected function finalExecute()
    {
        $result = $this->execute();
        $this->closeCursor();
        return $result;
    }

    /**
     * fetches the result from an executed query
     * @return array
     */
    protected function fetchAll()
    {
        return $this->stmt->fetchAll();
    }

    /**
     * returns a single line from the executed query
     * @return mixed
     */
    protected function fetch()
    {
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
     * @throws Exception table or view doesn't exist
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

        //Check if we have already passed the prefix
        if (!$this->startsWith($table, Config::TABLE_PREFIX)) {
            $table = $this->getTablePrefix($table);
        }


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
        throw new Exception("Table or view $table doesn't exist");
    }

    /**
     * This function adds the table prefix if set and returns the name
     * Use this if we are sure of the table name. Avoids the DB calls
     * @param $table string the table name
     * @return string
     */
    protected function getTablePrefix($table)
    {
        if (Config::TABLE_PREFIX != '') {
            $table = Config::TABLE_PREFIX . '_' . $table;
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
     * @throws Exception if debugging is on and no result
     */
    private function returnArray($result)
    {
        if ($result) {
            return $result;
        }
        if (Config::DEV_ENVIRONMENT) {
            throw new Exception("No results in database");
        }
        return [];
    }

    /**
     * gets the entire table or view and returns the array
     * @param string $table the table to search in, if empty then get the table based on model name
     * @return array the results from database
     * @throws \ReflectionException
     */
    protected function getResultSet($table = null): array
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
    protected function getResultSetLimited($limit, $table = null): array
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
    protected function getRowById($rowId, $table = null)
    {
        $tableName = $this->getTable($table);
        $idName = 'id' . str_replace(Config::TABLE_PREFIX."_","",$tableName);
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
     * @throws Exception if the column name consists of other characters than lower case, numbers and underscore for security
     */
    protected function getRowByColumn(String $columnName, $value, $table = null): array
    {
        $tableName = $this->getTable($table);
        $columnNameOk = preg_match("/^[a-z0-9_]+$/i",
            $columnName); //testing if column name only has lower case, numbers and underscore
        if (!$columnNameOk) {
            throw new Exception("Syntax error : Column name \"$columnName\" is not legal");
        }
        $sql = "SELECT * FROM $tableName WHERE $columnName = :value";
        $this->query($sql);
        $this->bind(':value', $value);
        $this->execute();
        $result = $this->stmt->fetch();
        return $this->returnArray($result);
    }

    /**
     * count the number of rows in table
     * @param string $table
     * @return mixed
     * @throws Exception
     */
    protected function count(string $table = null)
    {
        $table = $this->getTable($table);
        $sql = "SELECT COUNT(*) FROM $table";
        $this->query($sql);
        $this->execute();
        return $this->stmt->fetchColumn();
    }

    /**
     * get list with offset and limit from table
     * @param int $offset
     * @param int $limit
     * @param string|null $table
     * @return array
     * @throws \ReflectionException
     */
    protected function list(int $offset = 0, int $limit = Constant::POSTS_PER_PAGE, string $table = null)
    {
        $table = $this->getTable($table);
        $sql = "
            SELECT * FROM $table
            LIMIT :limit OFFSET :offset
        ";
        $this->query($sql);
        $this->bind(":limit", $limit);
        $this->bind(":offset", $offset);
        $this->execute();
        return $this->fetchAll();
    }

    /**
     * is the slug unique, used when updating the slug
     * @param string $slug the slug to search for
     * @param string $table the table to search in
     * @param string $slugColumn the name of the slug column
     * @return bool
     * @throws Exception
     */
    protected function isSlugUnique(string $slug, string $slugColumn, string $table = null): bool
    {
        if (!$this->isAlphaNum($slugColumn)) {
            throw new Exception("Invalid Column name");
        }

        $table = $this->getTable($table);

        $sql = "SELECT * FROM $table WHERE $slugColumn = :slug";
        $this->query($sql);
        $this->bind(':slug', $slug);
        $this->execute();
        return !$this->stmt->rowCount() > 0;
    }

    /**
     * get the ID of the row from the slug
     * @param string $slug the slug to search
     * @param string $table the table to search in
     * @param string $slugColumn the slug column name
     * @param string $idColumn the id column name
     * @return int the id of the row
     * @throws Exception
     */
    protected function getIdFromSlug(string $slug, string $idColumn, string $slugColumn, string $table = null): int
    {
        if (!$this->isAllAlphaNum([$idColumn, $slugColumn])) {
            throw new Exception("Invalid Column name");
        }

        $table = $this->getTable($table);

        $sql = "SELECT $idColumn FROM $table WHERE $slugColumn = :slug";
        $this->query($sql);
        $this->bind(":slug", $slug);
        $this->execute();
        if (!$this->stmt->rowCount() > 0) {
            return 0;
        }
        return $this->stmt->fetchColumn();

    }

    /**
     * get the slug from an Id
     * @param int $searchId
     * @param string $idColumn
     * @param string $slugColumn
     * @param string $table
     * @return string
     * @throws \ReflectionException
     */
    protected function getSlugFromId(
        int $searchId,
        string $idColumn,
        string $slugColumn,
        string $table = null
    ): string {

        if (!$this->isAllAlphaNum([$idColumn, $slugColumn])) {
            throw new Exception("Invalid Column name");
        }
        $table = $this->getTable($table);

        $sql = "SELECT $slugColumn FROM $table WHERE $idColumn = :searchId";
        $this->query($sql);
        $this->bind(":searchId", $searchId);
        $this->execute();
        if (!$this->stmt->rowCount() > 0) {
            return 0;
        }
        return $this->stmt->fetchColumn();
    }

    /**
     * get's the result of SELECT * FROM table where table_slug=$slug
     * @param string $slug the slug to look up
     * @param string $slugColumn the name of the slug column
     * @param string $table the table to search, if blank then we get the table or view based on the model name
     * @return array result or empty array
     * @throws \ReflectionException (probably not, but will throw an exception if debugging is on and no results)
     */
    protected function getRowBySlug(String $slug, string $slugColumn, $table = null): array
    {
        if (!$this->isAlphaNum($slugColumn)) {
            throw new Exception("Invalid Column name");
        }

        $table = $this->getTable($table);

        $sql = "SELECT * FROM $table WHERE $slugColumn = :slug";
        $this->query($sql);
        $this->bind(':slug', $slug);
        $this->execute();
        $result = $this->stmt->fetch();
        return $this->returnArray($result);
    }

    /**
     * generates a token to use
     * @return string
     * @throws \Exception
     */
    protected function generateToken():string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * generate a hash from a token
     * @param string $token
     * @return string
     */
    protected function generateHash(string $token):string
    {
        return hash_hmac("sha256", $token, Constant::HASH_KEY);
    }
}