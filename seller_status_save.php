<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
$status1 = $_REQUEST['status1'];
$status2 = $_REQUEST['status2'];
$empty = $_REQUEST['empty'];

if($status1 == 3 && $empty == 3){
	$taxi_addr = $_REQUEST['taxi_addr'];
	$taxi_zip = $_REQUEST['taxi_zip'];
	$taxi_dest = $_REQUEST['taxi_dest'];
}
else{
	$taxi_addr = '';
	$taxi_zip = '';
	$taxi_dest = '';
}
JavaScript::setCharset("UTF-8");

print_r($_REQUEST);
echo  "<br>";
if($status1 != ""){
	include './include/db_open.php';
	$sql = "UPDATE Member SET Status1='$status1', Empty='$empty', taxi_zip='$taxi_zip', taxi_addr='$taxi_addr', taxi_dest='$taxi_dest' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
	mysql_query($sql) or die (mysql_error());
	echo $sql . "<br>";
	switch($status1){
		case 1:
			mysql_query("UPDATE Product SET Status = 2 WHERE Status=6 AND Deliver=0 AND Member='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
			break;
		case 2:
			mysql_query("UPDATE Product SET Status = 6 WHERE Status=2 AND Deliver=0 AND Member='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
			break;
		case 3:
			mysql_query("UPDATE Product SET Status = 2 WHERE Status=6 AND Deliver=0 AND Member='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
			mysql_query("UPDATE Product SET Empty='$empty' WHERE Transport=1 AND Deliver=0 AND Member='" . $_SESSION['member']['No'] . "'") or die(mysql_error());
			break;
	}


	include './include/db_close.php';
}
if($status2 != ""){
	include './include/db_open.php';
	$sql = "UPDATE Member SET Status2='$status2' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
	mysql_query($sql) or die (mysql_error());

	$sql = "SELECT * FROM Member WHERE No='" . $_SESSION['member']['No'] . "'";
	$result = mysql_query($sql) or die(mysql_error());
	$member = mysql_fetch_array($result);
	if($member['Status2'] == 2){
		$sql = "UPDATE Member SET dateUpdate=CURRENT_TIMESTAMP, updateBy = '" . $_SESSION['member']['Name'] . "', Latitude1=latitude_app, Longitude1=longitude_app, Area1='0', Address1='' WHERE No = '" . $_SESSION['member']['No'] . "'";
		mysql_query($sql) or die (mysql_error());
		$_SESSION['Latitude1'] = $member['latitude_app'];
		$_SESSION['Longitude1'] = $member['longitude_app'];
		$_SESSION['Address1'] = "";
	}
	if($member['Status2'] == 1){
		$sql = "UPDATE Member SET dateUpdate=CURRENT_TIMESTAMP, updateBy = '" . $_SESSION['member']['Name'] . "', Latitude1=latitude_web, Longitude1=longitude_web, Area1=area_web, Address1=address_web WHERE No = '" . $_SESSION['member']['No'] . "'";
		mysql_query($sql) or die (mysql_error());
		$_SESSION['Latitude1'] = $member['latitude_web'];
		$_SESSION['Longitude1'] = $member['longitude_web'];
		$_SESSION['Address1'] = $member['address_web'];
	}
	echo $sql;






	include './include/db_close.php';
}
else{
//	JavaScript::Alert("輸入欄位不足!!");
}
?>
