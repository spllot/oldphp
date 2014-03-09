<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/tools.php';
require_once '../class/system.php';
require_once '../class/javascript.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->news][1])){exit("權限不足!!");}
$no =  Tools::parseInt2($_REQUEST["no"],0);
$subject = $_REQUEST["subject"];
$content = $_REQUEST["content"];
$date = $_REQUEST['date'];
if($date != "" && $subject != "" && $content != ""){
	include ("../include/db_open.php");

	if($no != "")
		mysql_query("UPDATE News SET Subject='$subject', Content='$content', Date='$date', dateUpdate=CURRENT_TIMESTAMP WHERE No = '$no'");
	else
		mysql_query("INSERT INTO News SET Subject='$subject', Content='$content', Date='$date', dateUpdate=CURRENT_TIMESTAMP");
	include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
//echo $sql;
JavaScript::Redirect("news.php?pageno=$pageno");
?>