<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
require_once("../class/javascript.php");
require_once("../class/tools.php");
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->queue][1])){exit("權限不足!!");}
$pageno = Tools::parseInt2($HTTP_POST_VARS["pageno"], 1);
$memberlist = $HTTP_POST_VARS["memberlist"];
$userid = $HTTP_POST_VARS["userid"];
if ($memberlist <> ""){
    include("../include/db_open.php");
    $sql = "DELETE FROM queueEMail WHERE No IN ($memberlist)";
    mysql_query($sql) or die("資料庫錯誤：" . mysql_error());
    include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
JavaScript::Redirect("queue.php?pageno=$pageno&userid=$userid");
?>