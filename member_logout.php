<?php
include './include/session.php';
require_once './class/javascript.php';

include './include/db_open.php';
$result = mysql_query("SELECT * FROM Member WHERE No='" . $_SESSION['member']['No'] . "'") or ide(mysql_error());
if($rs=mysql_fetch_array($result)){
	switch($rs['Status1']){
		case 1:
			mysql_query("UPDATE Product SET Status = 2 WHERE Status=6 AND Deliver=0 AND Member='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
			break;
		case 2:
			mysql_query("UPDATE Product SET Status = 6 WHERE Status=2 AND Deliver=0 AND Member='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
			break;
		case 3:
			mysql_query("UPDATE Product SET Status = 6 WHERE Status=2 AND Deliver=0 AND Member='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
			break;
	}
}
include './include/db_close.php';

unset($_SESSION['member']);
//session_destroy();
JavaScript::Execute("parent.setUserInfo();");
JavaScript::Execute("parent.mClk(4, null, 'product4.php');");
?>
