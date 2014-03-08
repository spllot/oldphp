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

$intro = $_REQUEST['intro'];

JavaScript::setCharset("UTF-8");
if($intro != ""){
	if(!empty($_SESSION['member'])){
		include './include/db_open.php';
		$sql = "UPDATE Member SET Intro='$intro' WHERE No='" . $_SESSION['member']['No'] . "'";
		mysql_query($sql) or die (mysql_error());
		include './include/db_close.php';
		JavaScript::Alert("資料已儲存!!");
	}
}
else{
	JavaScript::Alert("請輸入內容!!");
}
?>
