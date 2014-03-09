<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
require_once '../class/tools.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->catalog][1])){exit("權限不足!!");}

$itemno = $HTTP_POST_VARS["itemno"];
$diff = $HTTP_POST_VARS["sort"];
$pageno = Tools::parseInt2($HTTP_POST_VARS["pageno"], 1);
$usefor = $_REQUEST["usefor"];
$parent = $HTTP_POST_VARS["parent"];
$cat1 = $_REQUEST["cat1"];
$cat2 = $_REQUEST["cat2"];

if ($itemno > 0 && $diff != 0){
    include("../include/db_open.php");
	$sql = "SELECT Sort FROM Catalog WHERE No = '$itemno'";
	$result = mysql_query($sql) or die (mysql_error());
	if (list($curr_sort) = mysql_fetch_row($result)){
		$max_sort = 0;
		$sql = "SELECT max(Sort) FROM Catalog WHERE useFor = '$usefor'";
		$result = mysql_query($sql) or die (mysql_error());
		list($max_sort) = mysql_fetch_row($result);
		$new_sort = $curr_sort + $diff;
		if ($new_sort >= 0 && $new_sort <= $max_sort){
			mysql_query("UPDATE Catalog SET Sort = '$curr_sort' WHERE Sort = '$new_sort' AND useFor = '$usefor' AND Parent='$parent'") or die (mysql_error());
			mysql_query("UPDATE Catalog SET Sort = '$new_sort' WHERE No = '$itemno'") or die (mysql_error());
		}
	}
	include("../include/db_close.php");
}//if
else{
	JavaScript::Alert("輸入欄位不足!!" . $itemno . "-" . $diff);
}//else
JavaScript::Redirect("catalog.php?pageno=$pageno&usefor=$usefor&parent=$parent&cat1=$cat1&cat2=$cat2");
?>