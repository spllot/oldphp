<?php
include '../include/auth_admin.php';

require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->member][1])){exit("權限不足!!");}
require_once '../class/tools.php';
require_once '../class/javascript.php';
require_once '../class/admin.php';


$memberno = Tools::parseInt2($_POST["mno"], 0);
$pageno = Tools::parseInt2($_POST["pageno"], 1);
$keyword = $_REQUEST["keyword"];
$sort = $_REQUEST['sort'];
$order = $_REQUEST['order'];
$status = $_REQUEST["status"];
$level = $_REQUEST["level"];
$seller = $_REQUEST["seller"];
//$status = $_REQUEST["status"];
$page = new Admin();
$page->setHeading("會員維護");
$page->addContentFile("../html/member_property.html");
$page->addJSFile("../js/net.freesky.ultra.form.js");
$page->show();
include("../include/db_open.php");


JavaScript::setValue("join.pageno", $pageno);
JavaScript::setValue("join.keyword", $keyword);
JavaScript::setValue("join.status", $status);
JavaScript::setValue("join.level", $level);
JavaScript::setValue("join.seller", $seller);
JavaScript::setValue("join.sort", $sort);
JavaScript::setValue("join.order", $order);
if ($memberno > 0){
    $result=mysql_query("SELECT *, (SELECT Name FROM Catalog WHERE No = Member.subscribeArea) AS Area FROM Member WHERE No = $memberno");
	if ($rs = mysql_fetch_array($result)){
//		echo $status;
		JavaScript::setValue("join.userid", $rs["userID"]);
		JavaScript::setValue("join.userpass", $rs["userPass"]);
		JavaScript::setValue("join.level1", $rs["Level"]);
		JavaScript::setValue("join.days", $rs["Days"]);
		JavaScript::setValue("join.name", $rs["Name"]);
		JavaScript::setValue("join.address", $rs["Address"]);
		JavaScript::setValue("join.latitude", $rs["Latitude"]);
		JavaScript::setValue("join.longitude", $rs["Longitude"]);
		JavaScript::setValue("join.phone", $rs["Phone"]);
		JavaScript::setValue("join.referral", $rs["Referral"]);
		JavaScript::setValue("join.subscribe", (($rs["Subscribe"] == "1") ? "訂閱" : "未訂閱"));
		JavaScript::setValue("join.subscribearea", $rs["Area"]);
		JavaScript::setValue("join.dateRegister", $rs["dateRegister"]);
		JavaScript::setValue("join.dateConfirm", (($rs["dateConfirm"]=="0000-00-00 00:00:00") ? "尚未" :$rs["dateConfirm"]));
		JavaScript::setValue("join.dateLogin", (($rs["dateLogin"]=="0000-00-00 00:00:00") ? "尚未" :$rs["dateLogin"]));
		JavaScript::setValue("join.ipLogin", (($rs["ipLogin"]=="") ? "尚未" :$rs["ipLogin"]));

		JavaScript::setValue("join.rname", $rs["rName"]);
		JavaScript::setValue("join.raddress", $rs["rAddress"]);
		JavaScript::setValue("join.rlatitude", $rs["rLatitude"]);
		JavaScript::setValue("join.rlongitude", $rs["rLongitude"]);
		JavaScript::setValue("join.rzip", $rs["rZip"]);
		JavaScript::setValue("join.bank", $rs["Bank"]);
		JavaScript::setValue("join.unino", $rs["uniNo"]);
		JavaScript::setValue("join.branch", $rs["Branch"]);
		JavaScript::setValue("join.account", $rs["Account"]);
		JavaScript::setValue("join.dateRequest", (($rs["dateRequest"]=="0000-00-00 00:00:00") ? "尚未" :$rs["dateRequest"]));
		JavaScript::setValue("join.dateApprove", (($rs["dateApprove"]=="0000-00-00 00:00:00") ? "尚未" :$rs["dateApprove"]));
		if($rs["Status"]=="1"){
			JavaScript::setDisabled("join.btnActive");
		}
		else{
			JavaScript::setDisabled("join.btnDActive");
			JavaScript::setDisabled("join.btnDApprove");
			JavaScript::setDisabled("join.btnApprove");
		}
		if($rs["dateApprove"]=="0000-00-00 00:00:00"){
			JavaScript::setDisabled("join.btnDApprove");
		}
		else{
			JavaScript::setDisabled("join.btnApprove");
		}
	}
}
else{
//	JavaScript::setDisabled("join.btnCreate");
}
include("../include/db_close.php");
?>


