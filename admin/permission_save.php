<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
require_once '../class/tools.php';
require_once '../class/system.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->permission][1])){exit("權限不足!!");}
$group = Tools::parseInt2($HTTP_POST_VARS['group'], 0);
$itemlist = Tools::parseString2($HTTP_POST_VARS['itemlist'], "");
if($group > 0){
	include '../include/db_open.php';
	mysql_query("DELETE FROM Permission WHERE groupNo = '$group'") or die (mysql_error());
	$items = explode(",", $itemlist);
	for($i = 0; $i<sizeof($items); $i++){
		if($items[$i] != "")
			mysql_query("INSERT INTO Permission (groupNo, Module) VALUES ('$group', '" . $items[$i] . "')") or die(mysql_error());
	}
	include '../include/db_close.php';
	JavaScript::Alert("設定完成!");
}
else{
	JavaScript::Alert("輸入欄位不足!" . $group);
}

?>