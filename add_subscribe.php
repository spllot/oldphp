<?php
include './include/session.php';
require_once './class/javascript.php';
require_once './class/tools.php';
JavaScript::setCharset("UTF-8");

$product = $_REQUEST['product'];
$email = $_REQUEST['email'];

if(!Tools::checkEMail($email)){JavaScript::Alert("EMail格式錯誤!");exit;}
include './include/db_open.php';
if($product != "" && $email != ""){
	mysql_query("INSERT INTO Subscribe(EMail, Product, dateAdded) VALUES('$email', '$product', CURRENT_TIMESTAMP)");
	JavaScript::Alert('已加入訂閱名單!');
}

include './include/db_close.php';
?>