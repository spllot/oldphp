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
else if($_SESSION['member']['Seller'] != 2){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("申請成為賣家後; 需做登出動作; 然後才可正常使用[我是賣家]功能!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
include './include/db_open.php';

$curr = $_REQUEST['curr'];
list($Y, $M) = explode("-", $curr);
$type = $_REQUEST['type'];
$title = $_REQUEST['title'];
JavaScript::setCharset("UTF-8");
if($type != "" && $Y != "" && $M != ""){
	$today = date('Y-m-d');
	if($today >= $curr."-05"){
		JavaScript::Alert("日期錯誤，無法變更設定!");
	}
	else{
		$result = mysql_query("SELECT * FROM logReceipt WHERE Y='$Y' AND M='$M' AND Seller='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
		if($rs=mysql_fetch_array($result)){
			if($rs['ID'] != ""){
				JavaScript::Alert("發票已開立，無法變更設定!");
			}
			else{
				$sql = "UPDATE logReceipt SET Type='$type', Title='$title' WHERE Y='$Y' AND M='$M' AND Seller='" . $_SESSION['member']['No'] . "'";
				mysql_query($sql) or die(mysql_error());
				JavaScript::Alert("已設定!");
				//echo $sql;
			}
		}
		else{
			$sql = "INSERT INTO logReceipt SET Type='$type', Title='$title', Y='$Y', M='$M', Seller='" . $_SESSION['member']['No'] . "'";
			mysql_query($sql) or die(mysql_error());
			JavaScript::Alert("已設定!");
			//echo $sql;
		}
	}
}
include './include/db_close.php';

?>