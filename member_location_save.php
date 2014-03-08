<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
/*
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Redirect("./member_login.php?url=".urlencode($_SERVER['PHP_SELF']));
	exit;
}
*/
$latitude = explode(",", str_replace(array("(", ")", " "), "", $_REQUEST['latitude']));
$address = $_REQUEST['address'];
$lat = $_REQUEST['lat'];
$long = $_REQUEST['long'];
JavaScript::setCharset("UTF-8");
$url = (($_REQUEST['url'] == "") ? "member_location.php" : urldecode($_REQUEST['url']));
if($address != "" || ($lat != "" && $long != "")){
	if($lat != "" && $long != ""){
		$latitude[0] = $lat;
		$latitude[1] = $long;
	}
	if(!empty($_SESSION['member'])){
		include './include/db_open.php';
		$sql = "UPDATE Member SET Address='$address', dateUpdate=CURRENT_TIMESTAMP, updateBy = '" . $_SESSION['member']['Name'] . "', Latitude='" . $latitude[0] . "', Longitude='" . $latitude[1] . "' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
		$_SESSION['member']['Address'] = $address;
		$_SESSION['member']['Latitude'] = $latitude[0];
		$_SESSION['member']['Longitude'] = $latitude[1];
		mysql_query($sql) or die (mysql_error());
		include './include/db_close.php';
	}
	$_SESSION['Address'] = $address;
	$_SESSION['Latitude'] = $latitude[0];
	$_SESSION['Longitude'] = $latitude[1];
	JavaScript::Alert("現在位置已更新!");
	JavaScript::Execute("window.parent.location.href='member_location.php?var=" . time() . "'");
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
