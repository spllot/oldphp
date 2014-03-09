<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->blog][1])){exit("權限不足!!");}
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
echo "<script language=\"javascript\">\n";
$curr = $_REQUEST['curr'] . "S";
$tab = $_REQUEST['tab'];
$price = $_REQUEST['price'];
include("../include/db_open.php");
$result = mysql_query("SELECT * FROM Config WHERE ID='$curr'");
if(mysql_num_rows($result) > 0){
	mysql_query("UPDATE Config SET YN = '$price' WHERE ID = '$curr'");
}
else{
	mysql_query("INSERT INTO Config SET YN = '$price', ID = '$curr'");
}
include("../include/db_close.php");
echo "window.location.href=\"blog.php?tab=$tab\";\n";
echo "</script>\n";

?>
