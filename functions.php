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
    $xmlProfile = utf8_decode($xmlProfile);
    $xml = simplexml_load_string($xmlProfile,'SimpleXMLElement',LIBXML_NOCDATA);
    $json = json_encode($xml);
    $result = json_decode($json,true);

    return $result;
}

/**
 * Escaping MySQL strings without connection 
 *
 * @param string $unescaped Query unescaped
 * @return string
 */
function mysql_escape_mimic($unescaped) {
    $replacements = array(
        "\x00" => '\x00',
        "\n"   => '\n',
        "\r"   => '\r',
        "\\"   => '\\\\',
        "'"    => "\'",
        '"'    => '\"',
        "\x1a" => '\x1a'
    );

    return strtr($unescaped,$replacements);
}
?>