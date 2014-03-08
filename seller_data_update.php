<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
/**/

if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
$latitude = explode(",", str_replace(array("(", ")", " "), "", $_REQUEST['latitude']));
$address = $_REQUEST['address'];
$lat = $_REQUEST['lat'];
$area = $_REQUEST['area'];
$long = $_REQUEST['long'];
JavaScript::setCharset("UTF-8");
if($address != "" || ($lat != "" && $long != "")){
	if($lat != "" && $long != ""){
		$latitude[0] = $lat;
		$latitude[1] = $long;
	}
	if(!empty($_SESSION['member'])){
		include './include/db_open.php';
		$sql = "UPDATE Member SET area_web='$area', address_web='$address', dateUpdate=CURRENT_TIMESTAMP, updateBy = '" . $_SESSION['member']['Name'] . "', latitude_web='" . $latitude[0] . "', longitude_web='" . $latitude[1] . "' WHERE userID = '" . $_SESSION['member']['userID'] . "'";

		mysql_query($sql) or die(mysql_error());


		$sql = "SELECT * FROM Member WHERE No='" . $_SESSION['member']['No'] . "'";
		$result = mysql_query($sql) or die(mysql_error());
		$member = mysql_fetch_array($result);


		if($member['Status2'] == 1){
			$sql = "UPDATE Member SET Latitude1=latitude_web, Longitude1=longitude_web, Address1=address_web, Area1=area_web WHERE userID = '" . $_SESSION['member']['userID'] . "'";
			mysql_query($sql) or die(mysql_error());
		}

/*
		if($member['Status2'] == 2){
			$sql = "UPDATE Member SET dateUpdate=CURRENT_TIMESTAMP, updateBy = 'APP', Latitude1=latitude_app, Longitude1=longitude_app WHERE No = '" . $_SESSION['member']['No'] . "'";
			mysql_query($sql) or die (mysql_error());
			$_SESSION['Latitude1'] = $member['latitude_app'];
			$_SESSION['Longitude1'] = $member['longitude_app'];
			$_SESSION['Address1'] = "";
		}
		if($member['Status2'] == 1){
			$sql = "UPDATE Member SET dateUpdate=CURRENT_TIMESTAMP, updateBy = 'APP', Latitude1=latitude_web, Longitude1=longitude_web, Address1=address_web, Area1=area_web WHERE No = '" . $_SESSION['member']['No'] . "'";
			mysql_query($sql) or die (mysql_error());
			$_SESSION['Latitude1'] = $member['latitude_web'];
			$_SESSION['Longitude1'] = $member['longitude_web'];
			$_SESSION['Address1'] = $member['address_web'];
		}
		mysql_query($sql) or die (mysql_error());
*/
		include './include/db_close.php';
	}
	JavaScript::Alert("行動商店位置已更新!");
	JavaScript::Execute("window.parent.location.href='seller_status.php?var=" . time() . "'");
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
