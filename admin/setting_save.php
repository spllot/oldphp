<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->setting][1])){exit("權限不足!!");}
$id = array('coupon', 'welcome', 'ad', 'ad2', 'ad_picpath1', 'ad_picpath2', 'ad_picpath3', 'link1', 'link2', 'link3', 'exp', 'pics1', 'pics2', 'scroll', 'fee1', 'fee2', 'fee3', 'logo', 'ad2_auto', 'seller_policy', 'urlA', 'urlB', 'urlC', 'urlD', 'urlE', 'urlF', 'urlG', 'urlH', 'adfee1', 'adfee2', 'cashflow', 'showimg1', 'admax1', 'imgurl0', 'imgurl1', 'imgurl2', 'imgurl3', 'imgurl8', 'ad_picpath8', 'link8');//, 'showimg5', 'showimg6', 'showimg7', 'ad_picpath5', 'ad_picpath6', 'ad_picpath7', 'imgurl5', 'imgurl6', 'imgurl7');
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
echo "<script language=\"javascript\">\n";
include("../include/db_open.php");
for($i=0; $i<sizeof($id); $i++){
	if($_REQUEST[$id[$i]] != "" || substr($id[$i], 0, 6) == "imgurl"){
		mysql_query("UPDATE Config SET YN = '" . $_REQUEST[$id[$i]] . "' WHERE ID = '" . $id[$i] . "'");
//		echo "UPDATE Config SET YN = '" . $_REQUEST[$id[$i]] . "' WHERE ID = '" . $id[$i] . "'" . "<br>";
	}
}

if($_REQUEST['cashflow'] == "N"){
	mysql_query("UPDATE Member SET Seller=0 AND dateApprove = '0000-00-00 00:00:00'") or die(mysql_error());
}


include("../include/db_close.php");
echo "window.location.href=\"setting.php\";\n";
echo "</script>\n";

?>
