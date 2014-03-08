<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
/**/

if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
JavaScript::setCharset("UTF-8");
if(!empty($_SESSION['member'])){
	include './include/db_open.php';
	$sql = "UPDATE Member SET Address1='', dateUpdate=CURRENT_TIMESTAMP, updateBy = '" . $_SESSION['member']['Name'] . "', Latitude1='', Longitude1='', Area1 = '', latitude_web='', longitude_web='', address_web='', area_web='' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
	$_SESSION['member']['Address1'] = '';
	$_SESSION['member']['Latitude1'] = '';
	$_SESSION['member']['Longitude1'] = '';
	mysql_query($sql) or die (mysql_error());
	include './include/db_close.php';
}
JavaScript::Alert("行動商店位置已清除!");
JavaScript::Execute("window.parent.location.href='seller_status.php?var=" . time() . "'");
?>
