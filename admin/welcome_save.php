<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/tools.php';
require_once '../class/system.php';
require_once '../class/javascript.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->welcome][1])){exit("權限不足!!");}
$no =  Tools::parseInt2($_REQUEST["no"],0);
$subject = $_REQUEST["subject"];
$content = $_REQUEST["content"];
$usefor = $_REQUEST['usefor'];
$welcome_pic = $_REQUEST['logo'];
if($content != ""){
	include ("../include/db_open.php");
	mysql_query("UPDATE Page SET Content='$content' WHERE useFor = 'WELCOME'");
	mysql_query("UPDATE Config SET YN = '" . $welcome_pic . "' WHERE ID = 'welcome_pic'");
	include("../include/db_close.php");
    JavaScript::Alert("資料已儲存!!");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
//echo $sql;
JavaScript::Redirect("welcome.php");
?>