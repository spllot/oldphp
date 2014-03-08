<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

include './include/db_open.php';
$no = $_REQUEST['no'];

$sql = "DELETE FROM Message WHERE `To` = '" . $_SESSION['member']['userID'] . "' AND No = '$no'";
mysql_query($sql) or die(mysql_error());


include './include/db_close.php';
?>
<script language='javascript'>
window.parent.location.reload();
</script>