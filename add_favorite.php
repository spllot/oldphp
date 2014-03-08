<?php
include './include/session.php';
require_once './class/javascript.php';
JavaScript::setCharset("UTF-8");
if(empty($_SESSION['member'])){
	JavaScript::Alert("請先登入!");
	exit;
}



$product = $_REQUEST['product'];
include './include/db_open.php';
if($product != ""){
	mysql_query("INSERT INTO Favorite(Member, Product, dateAdded) VALUES('" . $_SESSION['member']['No'] . "', '$product', CURRENT_TIMESTAMP)");
	JavaScript::Alert('已加入收藏追蹤!');
}

include './include/db_close.php';
?>