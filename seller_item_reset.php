<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

$item = $_REQUEST['item'];
$type = $_REQUEST['type'];

JavaScript::setCharset("UTF-8");
if($item != ""){
	if(!empty($_SESSION['member'])){
		include './include/db_open.php';
		$today = date("Y-m-d 00-00-00");
		$sql = "UPDATE Product SET coupon_YN='0', coupon_name='', coupon_info='', coupon_quota='', coupon_generate='' WHERE Member='" . $_SESSION['member']['No'] . "' AND No in($item)";
		mysql_query($sql) or die (mysql_error());
		$sql = "DELETE FROM Coupon WHERE Product IN (SELECT No FROM Product WHERE Member='" . $_SESSION['member']['No'] . "' AND No IN ($item))";
		mysql_query($sql) or die (mysql_error());
		echo $sql;
		include './include/db_close.php';
	}
	JavaScript::Execute("window.parent.location.href='seller_item2.php?type=$type&var=" . time() . "'");
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
