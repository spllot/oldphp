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

$email = $_REQUEST['email'];
$subscribe = $_REQUEST['subscribe'];
$subscribearea = $_REQUEST['subscribe_area'];
JavaScript::setCharset("UTF-8");
include './include/db_open.php';

$sql = "UPDATE Member SET Subscribe='$subscribe', subscribeArea='$subscribearea', dateUpdate=CURRENT_TIMESTAMP, updateBy = '" . $name . "' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
mysql_query($sql) or die (mysql_error());

if($email != ""){
	mysql_query("INSERT INTO Subscribe(EMail, Product, dateAdded) VALUES('$email', '', CURRENT_TIMESTAMP)");
}
JavaScript::Alert("資料已更新!");
JavaScript::setURL("member_apply.php" ,"window.parent");
include './include/db_close.php';
?>
