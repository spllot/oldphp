<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->epaper][1])){exit("權限不足!!");}
$to = $_REQUEST['to'];
$recipients = explode(";", $_REQUEST['recipients']);
$subject = $_REQUEST['subject'];
$content = $_REQUEST['content'];
$daterequest = $_REQUEST['date'] . " " . $_REQUEST['hour'] . ":" . $_REQUEST['min'] . ":00";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
echo "<script language=\"javascript\">\n";
if($subject != "" && $content != ""){
	include("../include/db_open.php");
	$result = mysql_query("SELECT DISTINCT EMail FROM Subscribe");
	while($rs = mysql_fetch_array($result)){
		$cancel = "<div style=\"height:30px; line-height:30px\">您的Email:{$rs['EMail']}收到郵件是因為您已經訂閱了我們的電子報，如要<a href=\"http://{$WEB_HOST}/unsubscribe.php?email={$rs['EMail']}\">取消請按此</div>";
		$sql = "insert into queueEMail(Subject, Content, Recipient, Memo, dateRequested, dateSent) VALUES ('$subject', '" . $cancel . $content . "', '" . $rs['EMail'] . "', '', '$daterequest', '0000-00-00 00:00:00')";
		mysql_query($sql) or die (mysql_error());
	}
	include("../include/db_close.php");
	echo "alert(\"已加入發送排程!\")\n";
}
else{
	echo "alert(\"輸入欄位不足!\")\n";
}
echo "window.location.href=\"epaper.php\";\n";
echo "</script>\n";

?>
