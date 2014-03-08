<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';

$area=$_REQUEST['area'];
$pageno=$_REQUEST['pageno'];
$item=$_REQUEST['item'];

JavaScript::setCharset("UTF-8");

if ($_SESSION['member']['isAdmin'] == 1){
	if($item != ""){
		include './include/db_open.php';
		$sql = "DELETE FROM Supplier WHERE No IN ($item)";
		mysql_query($sql) or die (mysql_error());
		JavaScript::Alert("資料已刪除!");
		include './include/db_close.php';
	}
	else{
		JavaScript::Alert("輸入欄位不足!!");
	}
}
	JavaScript::Redirect("contact.php?area=$area&pageno=$pageno");
?>
