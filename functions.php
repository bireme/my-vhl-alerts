<?php
/**
 * Functions File
 *
 * @package     Minha BVS - Alertas
 * @author      Wilson da Silva Moura (mourawil@paho.org)
 * @copyright   BIREME/PAHO/WHO
 *
 */

/**
 * Convert XML string to Array
 *
 * @param string $xmlProfile
 * @return array
 */
function xmlstr_to_array($xmlProfile){
    /* load simpleXML object */
    $xmlProfile = utf8_decode(str_replace('&', '&amp;', $xmlProfile));
    $xml = simplexml_load_string($xmlProfile,'SimpleXMLElement',LIBXML_NOCDATA);
    $json = json_encode($xml);
    $result = json_decode($json,true);

    return $result;
}

/**
 * Encrypt a given string
 *
 * @param string $text
 * @param string $cKey Encryption salt
 * @return string
 */
function encrypt($text,$cKey=CRYPT_KEY){
    return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,
                $cKey, $text, MCRYPT_MODE_ECB,
                mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, 
                        MCRYPT_MODE_ECB), MCRYPT_RAND))));    
}

/**
 * Generate a user token
 *
 * @param string $userID
 * @param string $sysUID
 * @return string
 */
function makeUserTK($userID,$sysUID){
    return encrypt($userID.CRYPT_SEPARATOR.$sysUID, CRYPT_PUBKEY);
}

/**
 * Get alerts language
 *
 * @param string $country Country code
 * @return string
 */
function getAlertsLanguage($country){
    global $c_pt;
    global $c_es;
    
    $code = 'en';

    if ( array_key_exists($country, $c_pt) ) {
        $code = 'pt';
    } elseif ( array_key_exists($country, $c_es) ) {
        $code = 'es';
    }

    return $code;
}
?>