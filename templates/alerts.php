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

$alerts = new Alerts();
$alerts->make_template($id);

?>