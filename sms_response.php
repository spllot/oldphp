<?php
$kmsgid = $_REQUEST["kmsgid"];
$tdstaddr = $_REQUEST["dstaddr"];
$tdlvtime = $_REQUEST["dlvtime"];
$tdonetime = $_REQUEST["donetime"];
$tstatusstr = $_REQUEST["statusstr"];

if($kmsgid > 0){
	include './include/db_open.php';
	$sql = "UPDATE logSMS SET Status = '$tstatusstr' WHERE Message = '$kmsgid'";
	mysql_query($sql) or die (mysql_error());
	include './include/db_close.php';
}
?>