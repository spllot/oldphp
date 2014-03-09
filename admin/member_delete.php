<?php
include '../include/auth_admin.php';
require_once("../class/javascript.php");
require_once("../class/tools.php");
require_once '../class/system.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->member][1])){exit("權限不足!!");}
$pageno = Tools::parseInt2($_POST["pageno"], 1);
$memberlist = $_POST["memberlist"];
$status = $_REQUEST["status"];
$keyword = $_REQUEST["keyword"];
$status = $_REQUEST["status"];
$level = $_REQUEST["level"];
$seller = $_REQUEST["seller"];
$sort = $_REQUEST['sort'];
$order = $_REQUEST['order'];
if ($memberlist <> ""){
    include("../include/db_open.php");
    $sql = "DELETE FROM Member WHERE No IN ($memberlist)";
    mysql_query($sql) or die("資料庫錯誤：" . mysql_error());
//    JavaScript::Alert("資料已刪除");
    include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
JavaScript::Redirect("member.php?status=$status&level=$level&seller=$seller&pageno=$pageno&status=$status&keyword=$keyword&sort=$sort&order=$order");
?>