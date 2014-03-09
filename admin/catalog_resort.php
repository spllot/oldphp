<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
require_once '../class/tools.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->catalog][1])){exit("權限不足!!");}

$itemlist = $HTTP_POST_VARS["memberlist"];
$pageno = Tools::parseInt2($HTTP_POST_VARS["pageno"], 1);
$parent = $HTTP_POST_VARS["parent"];
$usefor = $_REQUEST["usefor"];
$cat1 = $_REQUEST["cat1"];
$cat2 = $_REQUEST["cat2"];
if ($itemlist != ""){
    include("../include/db_open.php");
	$items = explode(",", $itemlist);
	for($i = 0; $i<sizeof($items); $i++){
		$sql = "UPDATE Catalog SET Sort = '" . $HTTP_POST_VARS["sort_" . $items[$i]] . "' WHERE No = '" . $items[$i] . "'";
		mysql_query($sql) or die (mysql_error());
	}
	include("../include/db_close.php");
}//if
else{
	JavaScript::Alert("輸入欄位不足!!");
}//else
JavaScript::Redirect("catalog.php?pageno=$pageno&usefor=$usefor&parent=$parent&cat1=$cat1&cat2=$cat2");
?>