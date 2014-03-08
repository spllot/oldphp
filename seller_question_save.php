<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
$no = $_REQUEST['no'];
$reply = $_REQUEST['reply'];
JavaScript::setCharset("UTF-8");
if($reply != "" && $no != ""){
	include './include/db_open.php';
	$sql = "UPDATE logComment SET Reply='$reply', dateReplied=CURRENT_TIMESTAMP, replyBy = '" . $_SESSION['member']['No'] . "' WHERE Owner = '" . $_SESSION['member']['No'] . "' AND No='$no' AND Question='1'";
	mysql_query($sql) or die (mysql_error());
	JavaScript::Execute("window.parent.location.reload();");
	include './include/db_close.php';
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
