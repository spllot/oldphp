<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

$rate = $_REQUEST['rate'];
$product = $_REQUEST['product'];
$content = $_REQUEST['content'];
JavaScript::setCharset("UTF-8");
if($content != "" && $product != "" && $rate != ""){
	include './include/db_open.php';
	$sql = "insert into logRating SET Quality = '$rate', transactionNo = '$product', Content='$content', rateBy='" . $_SESSION['member']['No'] . "', Owner=(SELECT Member FROM Product WHERE No = '$product'), dateRated=CURRENT_TIMESTAMP";

	$result = mysql_query($sql) or die (mysql_error());
	include './include/db_close.php';
	JavaScript::Execute("window.parent.location.reload();");
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
