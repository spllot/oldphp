<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
require_once '../class/tools.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->group][1])){exit("權限不足!!");}

$itemlist = $HTTP_POST_VARS["memberlist"];
$pageno = Tools::parseInt2($HTTP_POST_VARS["pageno"], 1);
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
JavaScript::Redirect("group.php?pageno=$pageno");
?>