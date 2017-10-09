<?php
/**
 * Profiles Class
 *
 * @package     Minha BVS - Alertas
 * @author      Wilson da Silva Moura (mourawil@paho.org)
 * @copyright   BIREME/PAHO/WHO
 *
 */

require_once __DIR__ . '/db.class.php';

class Profiles {

    public function __construct() {}

    /**
     * get user's profiles
     *
     * @param int $sysUID user sysUID
     * @return array object Profile list
     */
    public static function get_profiles_list($sysUID){
        global $_conf;
        $retValue = false;
        
        $strsql = "SELECT * FROM  profiles WHERE sysUID = '".$sysUID."'";

        try{
            $_db = new DB();
            $result = $_db->exec_query($strsql);
        }catch(DBException $e){
            $logger = &Log::singleton('file', LOG_FILE, __CLASS__, $_conf);
            $logger->log($e->getMessage(),PEAR_LOG_EMERG);
        }

        if ( count($result) !== 0 ) $retValue = $result;
        
        return $retValue;
    }

    /**
     * get one specified user profile
     *
     * @param int $sysUID user sysUID
     * @param string $profileID profile id
     * @return array object Profile
     */
    public static function get_profile($sysUID,$profileID){
        $retValue = false;

        $strsql = "SELECT * FROM profiles
            WHERE sysUID = '".$sysUID."'
            AND profileID='".$profileID."'";

        try{
            $_db = new DB();
            $result = $_db->exec_query($strsql);
        }catch(DBException $e){
            $logger = &Log::singleton('file', LOG_FILE, __CLASS__, $_conf);
            $logger->log($e->getMessage(),PEAR_LOG_EMERG);
        }
        
        if ( count($result) !== 0 ) $retValue = $result;
        
        return $retValue;
    }

}
?>