<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	exit;
}
$no=$_REQUEST['no'];
$s=$_REQUEST['s'];
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Orders WHERE No='$no' AND Seller='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
if($tems=mysql_fetch_array($result)){
	mysql_query("UPDATE Orders SET dateReceipt = '$s' WHERE No='" . $no . "'");
}
include './include/db_close.php';
?>
