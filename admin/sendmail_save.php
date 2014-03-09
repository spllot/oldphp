<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->sendmail][1])){exit("權限不足!!");}
$to = $_REQUEST['to'];
$recipients = explode(";", $_REQUEST['recipients']);
$subject = $_REQUEST['subject'];
$content = $_REQUEST['content'];
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
echo "<script language=\"javascript\">\n";
if($subject != "" && $content != ""){
	include("../include/db_open.php");
	if($to == 4){
		for($i=0; $i<sizeof($recipients); $i++){
			$sql = "insert into queueEMail(Subject, Content, Recipient, Memo, dateRequested, dateSent) VALUES ('$subject', '$content', '" . $recipients[$i] . "', '', CURRENT_TIMESTAMP, '0000-00-00 00:00:00')";
			mysql_query($sql) or die (mysql_error());
		}
	}
	else{
		$sql = "insert into queueEMail(Subject, Content, Recipient, Memo, dateRequested, dateSent) SELECT '$subject', '$content', userID, '', CURRENT_TIMESTAMP, '0000-00-00 00:00:00' FROM Member";
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
		}
		mysql_query($sql) or die (mysql_error());
	}
	include("../include/db_close.php");
	echo "alert(\"已加入發送排程!\")\n";
}
else{
	echo "alert(\"輸入欄位不足!\")\n";
}
echo "window.location.href=\"sendmail.php\";\n";
echo "</script>\n";

?>
