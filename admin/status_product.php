<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->status][1])){exit("權限不足!!");}
include("../include/db_open.php");
$num=0;
$mode = $_REQUEST['mode'];
$deliver = $_REQUEST['deliver'];
$catalog = $_REQUEST['catalog'];

$sql = "SELECT COUNT(*) FROM Product WHERE dateClose >= CURRENT_TIMESTAMP AND Mode='$mode' AND Deliver='$deliver'";
$sql .= (($catalog!="") ? " AND Catalog = '$catalog'" : "");
$result = mysql_query($sql) or die(mysql_error());
list($num) = mysql_fetch_row($result);



echo $num;
include '../include/db_close.php';
?>