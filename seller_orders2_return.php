<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	exit;
}
$no=$_REQUEST['no'];
$sort=$_REQUEST['sort'];
$s=$_REQUEST['s'];
include './include/db_open.php';
$result = mysql_query("SELECT Items.* FROM Items INNER JOIN Orders ON Items.orderID = Orders.ID WHERE Items.No='$no' AND Orders.Seller='" . $_SESSION['member']['No'] . "' AND Items.Sort='$sort'") or die(mysql_error());
if($items=mysql_fetch_array($result)){
	mysql_query("UPDATE Items SET dateBack = '$s' WHERE No='" . $items['No'] . "'") or die(mysql_error());
}
else{
//	echo 'err';
}
include './include/db_close.php';
?>
