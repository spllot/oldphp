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
	mysql_query("DELETE FROM Favorite WHERE Member = '" . $_SESSION['member']['No'] . "' AND Product = '$product'");
}
JavaScript::Execute("window.parent.location.reload();");
include './include/db_close.php';
?>