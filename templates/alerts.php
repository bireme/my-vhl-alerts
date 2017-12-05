<?php

/**
 * Alert template output
 *
 * @package     Minha BVS - Alertas
 * @author      Wilson da Silva Moura (mourawil@paho.org)
 * @copyright   BIREME/PAHO/WHO
 *
 */

require_once __DIR__ . '/../class/alert.class.php';

$id = ( array_key_exists("id", $_REQUEST) ) ? $_REQUEST['id'] : FALSE;
$lang = ( array_key_exists("lang", $_REQUEST) ) ? $_REQUEST['lang'] : 'pt';

$alerts = new Alerts();
$alerts->make_template($id, $lang);

?>