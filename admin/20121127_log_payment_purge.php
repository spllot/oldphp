<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
require_once("../class/javascript.php");
require_once("../class/tools.php");
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->log_payment][1])){exit("權限不足!!");}
$pageno = Tools::parseInt2($HTTP_POST_VARS["pageno"], 1);
$memberlist = $HTTP_POST_VARS["memberlist"];
$userid = $HTTP_POST_VARS["userid"];
$dateline = date('Y-m-d 00:00:00');
 include("../include/db_open.php");
 $sql = "DELETE FROM Payment WHERE datePaid='0000-00-00 00:00:00' AND dateSubmited <'$dateline'";
 mysql_query($sql) or die("資料庫錯誤：" . mysql_error());
 include("../include/db_close.php");
JavaScript::Redirect("log_payment.php?pageno=$pageno&userid=$userid");
?>