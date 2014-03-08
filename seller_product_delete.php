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
/*
else if($_SESSION['member']['Seller'] != 2){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("申請成為賣家後; 需做登出動作; 然後才可正常使用[我是賣家]功能!");
	JavaScript::Redirect("./member_form.php");
	exit;
}
*/
$memberlist = $_REQUEST['itemlist'];
$mode = $_REQUEST['mode'];
$deliver = $_REQUEST['deliver'];
$activity = $_REQUEST['activity'];
$transport = $_REQUEST['transport'];
$pageno = $_REQUEST['pageno'];
JavaScript::setCharset("UTF-8");
if($memberlist != ""){
	include("./include/db_open.php");
	$sql = "DELETE FROM Product WHERE No IN ($memberlist) AND Member='" . $_SESSION['member']['No'] . "'";
	echo $sql;
	mysql_query($sql) or die("資料庫錯誤：" . mysql_error());
	include("./include/db_close.php");
	JavaScript::setURL("seller_product.php?mode=$mode&deliver=$deliver&activity=$activity&transport=$transport", "window.parent");
}
else{
	JavaScript::Alert("錯誤!");
}
?>