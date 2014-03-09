<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->setting][1])){exit("權限不足!!");}
$id = array('showimg5', 'showimg6', 'showimg7', 'ad_picpath5', 'ad_picpath6', 'ad_picpath7', 'imgurl5', 'imgurl6', 'imgurl7');
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
echo "<script language=\"javascript\">\n";
include("../include/db_open.php");
for($i=0; $i<sizeof($id); $i++){
	if($_REQUEST[$id[$i]] != "" || substr($id[$i], 0, 6) == "imgurl"){
		mysql_query("UPDATE Config SET YN = '" . $_REQUEST[$id[$i]] . "' WHERE ID = '" . $id[$i] . "'");
//		echo "UPDATE Config SET YN = '" . $_REQUEST[$id[$i]] . "' WHERE ID = '" . $id[$i] . "'" . "<br>";
	}
}


include("../include/db_close.php");
echo "window.location.href=\"setting2.php\";\n";
echo "</script>\n";

?>
