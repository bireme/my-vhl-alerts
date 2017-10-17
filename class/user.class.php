<?php
/**
 * Users Class
 *
 * @package     Minha BVS - Alertas
 * @author      Wilson da Silva Moura (mourawil@paho.org)
 * @copyright   BIREME/PAHO/WHO
 *
 */

require_once __DIR__ . '/db.class.php';

class Users {

    public function __construct() {}

    /**
     * Get a specific user data
     *
     * @param string $id User ID
     * @return array User data
     */
    public static function get_user_data($id){
        global $_conf;
        $retValue = FALSE;

        $strsql = "SELECT * FROM users WHERE sysUID = '".$id."'";

        try{
            $_db = new DB();
            $res = $_db->exec_query($strsql);
        }catch(DBException $e){
            $logger = &Log::singleton('file', LOG_FILE, __CLASS__, $_conf);
            $logger->log($e->getMessage(),PEAR_LOG_EMERG);
        }

        if ( $res && count($res) > 0 ) $retValue = $res[0];

        return $retValue;
    }

    /**
     * Get users
     *
     * @param boolean $only_active Get active users only
     * @param boolean $accept_mail
     * @return array Users data
     */
    public static function get_users($only_active=FALSE, $accept_mail=FALSE){
        global $_conf;
        $retValue = FALSE;

        $strsql = "SELECT sysUID, userID, userEMail, userFirstName, userLastName FROM users";

        if ( $only_active ) {
            $strsql .= " WHERE agreement_date <> ''
                    AND last_login IS NOT NULL
                    AND active = '1'";
        }

        if ( $accept_mail ) {
            $operator = $only_active ? ' AND ' : ' WHERE ';
            $strsql .= $operator . "accept_mail = '1'";
        }

        try{
            $_db = new DB();
            $res = $_db->exec_query($strsql);
        }catch(DBException $e){
            $logger = &Log::singleton('file', LOG_FILE, __CLASS__, $_conf);
            $logger->log($e->getMessage(),PEAR_LOG_EMERG);
        }

        if ( $res && count($res) > 0 ) $retValue = $res;

        return $retValue;
    }

    public static function get_sysuid($userID){
        global $_conf;

        $strsql = "SELECT sysUID FROM users WHERE userID = '".$userID."'";

        try{
            $_db = new DB();
            $res = $_db->exec_query($strsql);
        }catch(DBException $e){
            $logger = &Log::singleton('file', LOG_FILE, __CLASS__, $_conf);
            $logger->log($e->getMessage(),PEAR_LOG_EMERG);
        }
        
        return $res[0]['sysUID'] ? $res[0]['sysUID'] : FALSE;
    }

    /**
     * Check if the user is active
     *
     * @param string $userID user ID
     * @return boolean
     */
    public static function is_active($userID){
        global $_conf;
        $retValue = FALSE;

        $strsql = "SELECT count(userID) FROM users
                    WHERE userID = '".trim($userID)."'
                    AND agreement_date <> ''
                    AND last_login IS NOT NULL
                    AND active = '1'";

        try{
            $_db = new DB();
            $res = $_db->exec_query($strsql);
        }catch(DBException $e){
            $logger = &Log::singleton('file', LOG_FILE, __CLASS__, $_conf);
            $logger->log($e->getMessage(),PEAR_LOG_EMERG);
        }

        if ( $res[0]['count(userID)'] >= 1 ) $retValue = TRUE;

        return $retValue;
    }

    /**
     *  Get the total count of users
     *
     * @param boolean $only_active Get active users only
     * @param boolean $accept_mail
     * @return boolean|integer
     */
    public static function get_users_count($only_active=FALSE, $accept_mail=FALSE){
        global $_conf;
        $retValue = FALSE;

        $strsql= 'SELECT count(*) as total FROM users';

        if ( $only_active ) {
            $strsql .= " WHERE agreement_date <> ''
                    AND last_login IS NOT NULL
                    AND active = '1'";
        }

        if ( $accept_mail ) {
            $operator = $only_active ? ' AND ' : ' WHERE ';
            $strsql .= $operator . "accept_mail = '1'";
        }

        try{
            $_db = new DB();
            $res = $_db->exec_query($strsql);
        }catch(DBClassException $e){
            $logger = &Log::singleton('file', LOG_FILE, __CLASS__, $_conf);
            $logger->log($e->getMessage(),PEAR_LOG_EMERG);
        }

        if ( $res ) $retValue = $res[0]['total'];

        return $retValue;
    }

}
?>