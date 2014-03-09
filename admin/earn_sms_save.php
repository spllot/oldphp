<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->earn_sms][1])){exit("權限不足!!");}
$tab = $_REQUEST['tab'];
$coupon=$_REQUEST['coupon'];
$sms=$_REQUEST['sms'];
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
echo "<script language=\"javascript\">\n";
include("../include/db_open.php");
$result = mysql_query("SELECT YN FROM Config WHERE ID='sms'") or die(mysql_error());
if($rs=mysql_fetch_array($result)){
	$sms += $rs['YN'];
}



//echo $sms;
mysql_query("UPDATE Config SET YN = '" . $coupon . "' WHERE ID = 'coupon'");
mysql_query("UPDATE Config SET YN = '" . $sms . "' WHERE ID = 'sms'");



include("../include/db_close.php");
echo "window.location.href=\"earn_sms.php?tab=$tab\";\n";
echo "</script>\n";

?>
