<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->sendmsg][1])){exit("權限不足!!");}
$to = $_REQUEST['to'];
$recipients = explode(";", $_REQUEST['recipients']);
$subject = $_REQUEST['subject'];
$content = $_REQUEST['content'];
$type = $_REQUEST['type'];
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
echo "<script language=\"javascript\">\n";
if($subject != "" && $content != ""){
	include("../include/db_open.php");
	$sql = "insert into Message(Subject, Content, Sender, `To`, dateSent, Type) SELECT '$subject', '$content', 'service@intimego.com', userID, CURRENT_TIMESTAMP, '$type' FROM Member";
	switch($to){
		case 1:
			$sql .= " where 1=1";
			break;
		case 2:
			$sql .= " where Seller<>2";
			break;
		case 3:
			$sql .= " where Seller=2";
			break;
		case 4:
			$sql .= " where userID IN ('" . implode("','", $recipients) . "')";
			break;
	}
	mysql_query($sql) or die (mysql_error());
	include("../include/db_close.php");
	echo "alert(\"已發送!\")\n";
}
else{
	echo "alert(\"輸入欄位不足!\")\n";
}
echo "window.location.href=\"sendmsg.php\";\n";
echo "</script>\n";

?>
