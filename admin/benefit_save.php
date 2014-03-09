<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->benefit][1])){exit("權限不足!!");}
$to = $_REQUEST['to'];
$recipients = explode(";", $_REQUEST['recipients']);
$subject = $_REQUEST['subject'];
$content = $_REQUEST['content'];
$area = $_REQUEST['area_list'];
$daterequest = $_REQUEST['date'] . " " . $_REQUEST['hour'] . ":" . $_REQUEST['min'] . ":00";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
echo "<script language=\"javascript\">\n";
if($subject != "" && $content != ""){
	include("../include/db_open.php");
	$sql = "SELECT *, userID AS EMail FROM Member WHERE Subscribe = 1";
	$sql .= (($area != "") ? " AND subscribeArea IN ($area)" : "");
	$result = mysql_query($sql);
	$num = mysql_num_rows($result);
	if($num > 0){
		while($rs = mysql_fetch_array($result)){
			$cancel = <<<EOD
				<center>
				<span style="line-height:30px">這是您訂閱的好康資訊，如要<a href="http://{$WEB_HOST}/unsubscribe1.php?email={$rs['EMail']}">取消訂閱請按此</a>。(請勿回覆本信)</span>
				</center>
EOD;
			$sql = "insert into queueEMail(Subject, Content, Recipient, Memo, dateRequested, dateSent) VALUES ('$subject', '" . $cancel . $content . "', '" . $rs['EMail'] . "', '', '$daterequest', '0000-00-00 00:00:00')";
			mysql_query($sql) or die (mysql_error());
		}
		echo "alert(\"已加入發送排程!，共有{$num}位訂閱會員\")\n";
		echo "window.location.href=\"benefit.php\";\n";
	}
	else{
		echo "alert(\"查無訂閱會員!\")\n";
	}
	include("../include/db_close.php");
}
else{
	echo "alert(\"輸入欄位不足!\")\n";
}
echo "</script>\n";

?>
