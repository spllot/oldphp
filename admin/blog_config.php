<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->blog][1])){exit("權限不足!!");}
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
echo "<script language=\"javascript\">\n";
$curr = $_REQUEST['curr'];
$tab = $_REQUEST['tab'];
$amount = $_REQUEST['amount'];
include("../include/db_open.php");
$result = mysql_query("SELECT COUNT(*) FROM Blog WHERE dateSubmited LIKE '$curr%'");
list($now)=mysql_fetch_row($result);
if($now <= $amount){
	$result = mysql_query("SELECT * FROM Config WHERE ID='$curr'");
	if(mysql_num_rows($result) > 0){
		mysql_query("UPDATE Config SET YN = '$amount' WHERE ID = '$curr'");
	}
	else{
		mysql_query("INSERT INTO Config SET YN = '$amount', ID = '$curr'");
	}
}
else{
	echo "alert('目前徵求數($now)已超過($amount)，無法減量設定!');";
}
include("../include/db_close.php");
echo "window.location.href=\"blog.php?tab=$tab\";\n";
echo "</script>\n";

?>
