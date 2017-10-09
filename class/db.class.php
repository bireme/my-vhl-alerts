<?php
/**
 * Database Connection Class
 *
 * @package     Minha BVS - Alertas
 * @author      Wilson da Silva Moura (mourawil@paho.org)
 * @copyright   BIREME/PAHO/WHO
 *
 */

require_once __DIR__ . '/../includes.php';

/**
 * Custom exception Database Connection Class
 */
class DBException extends Exception {}

/**
 * Database Connection Class
 *
 * Handle the main activities to interact with the database.
 */
class DB {
    private $_conn = null;
    private $_host = DB_HOST;
    private $_user = DB_USERNAME;
    private $_password = DB_PASSWORD;
    private $_db = DB_DBNAME;

    /**
     * Create the connection with the database.
     */
    public function __construct(){
        $this->_conn = mysqli_connect($this->_host, $this->_user
            , $this->_password);

        if(!$this->_conn){
            throw new DBException('Err:connect:'.mysqli_error($this->_conn));
        }

        if(!mysqli_select_db($this->_conn, $this->_db)){
            throw new DBException('Err:selectDB:'.mysqli_error($this->_conn));
        }
    }

   /**
    * Execute the Insert queries
    *
    * @param  string $query
    * @return int
    */
    public function exec_insert($query){
        $result = mysqli_query($this->_conn, $query);

        if(!$result){
            throw new DBException('Err:ExecInsert:'.mysqli_error($this->_conn));
        }

        return(mysqli_insert_id($this->_conn));
    }

    /**
     * Execute the Update/Delete queries
     *
     * @param string $query
     * @return int
     */
    public function exec_update($query){
        $result = mysqli_query($this->_conn, $query);

        if(!$result){
            throw new DBException('Err:ExecUpdate:'.mysqli_error($this->_conn));
        }

        return(mysqli_affected_rows($this->_conn));
    }

    /**
     * Execute the Select queries
     * @param string $query
     * @return int
     */
    public function exec_query($query){
        $result = mysqli_query($this->_conn, $query);

        if(!$result){
            throw new DBException('Err:ExecQuery:'.mysqli_error($this->_conn));
        }

        $recordSet = array();

        while ($row = mysqli_fetch_assoc($result)) {
            array_push($recordSet, $row);
        }

        return($recordSet);
    }
}
?>