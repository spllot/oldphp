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
$result = mysql_query("SELECT Items.* FROM Items INNER JOIN Orders ON Items.orderID = Orders.ID WHERE Items.No='$no' AND Orders.Seller='" . $_SESSION['member']['No'] . "' AND Items.Sort='$sort' AND Items.Refund=0") or die(mysql_error());
if($tems=mysql_fetch_array($result)){
	mysql_query("UPDATE Orders SET dateShipped = '$s' WHERE ID='" . $tems['orderID'] . "' AND dateShipped='0000-00-00 00:00:00'");
}
include './include/db_close.php';
?>
