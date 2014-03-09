<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
require_once("../class/javascript.php");
require_once("../class/tools.php");
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->propose][1])){exit("權限不足!!");}
$pageno = Tools::parseInt2($_REQUEST["pageno"], 1);
$itemlist = $_REQUEST["memberlist"];
$parent = Tools::parseInt2($_REQUEST["parent"], 0);
$area = $_REQUEST['area'];
$catalog = $_REQUEST['catalog'];
$type = $_REQUEST['type'];
$tab = $_REQUEST['tab'];
$keyword = $_REQUEST['keyword'];
if ($itemlist <> ""){
		include("../include/db_open.php");
		$sql = "UPDATE Product SET Status = 3 WHERE No IN ($itemlist)";
		mysql_query($sql) or die("資料庫錯誤：" . mysql_error());
		include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
JavaScript::Redirect("propose.php?tab=$tab&pageno=$pageno&area=$area&type=$type&catalog=$catalog&keyword=$keyword");
?>