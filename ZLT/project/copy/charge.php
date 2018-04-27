<?php

$_DPOST = $_POST;
$_DGET = $_GET;
require 'config.inc.php';
require '../common.inc.php';
$chargereceiveurl = get_cookie('chargereceiveurl');
set_cookie('chargereceiveurl', '');

if (!empty($chargereceiveurl)) {
    message('', $chargereceiveurl);
	
    //require DT_ROOT . '/module/' . $module . '/chargeforlyk123.inc.php';
    //require $chargereceiveurl;
} else {
    require DT_ROOT . '/module/' . $module . '/charge.inc.php';
}
?>