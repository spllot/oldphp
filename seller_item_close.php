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

$item = $_REQUEST['item'];
$type = $_REQUEST['type'];

JavaScript::setCharset("UTF-8");
if($item != ""){
	if(!empty($_SESSION['member'])){
		include './include/db_open.php';
		$today = date("Y-m-d 00-00-00");
		$sql = "UPDATE Product SET dateClose='$today', Status=4 WHERE Member='" . $_SESSION['member']['No'] . "' AND No in($item)";
		mysql_query($sql) or die (mysql_error());
		include './include/db_close.php';
	}
	JavaScript::Execute("window.parent.location.href='seller_item.php?type=$type&var=" . time() . "'");
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
