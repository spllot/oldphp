<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/tools.php';
require_once '../class/system.php';
require_once '../class/javascript.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->page][1])){exit("權限不足!!");}
$no =  Tools::parseInt2($_REQUEST["no"],0);
$subject = $_REQUEST["subject"];
$content = $_REQUEST["content"];
$usefor = $_REQUEST['usefor'];
if($no!= "" && $subject != "" && $content != ""){
	include ("../include/db_open.php");
	mysql_query("UPDATE Page SET Subject='$subject', Content='$content' WHERE No = '$no'");
	include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
//echo $sql;
JavaScript::Redirect("page.php?pageno=$pageno&usefor=$usefor");
?>