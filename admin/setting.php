<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->setting][1])){exit("權限不足!!");}
$page = new Admin();

$tab = 0;
$menu = array(
	'setting.php' =>'設定1',
	'setting2.php' =>'設定2',
);

$page = new Admin();
$page->addJSFile("../js/common_admin.js");
$page->setHeading($menu, $tab);





$page->addJSFile("/js/jquery.js");
$page->addJSFile("/js/ajaxupload.js");
$page->addcontentFile("../html/setting_admin.html");
$page->show();
include '../include/db_open.php';
$result = mysql_query("SELECT * FROM Config WHERE ID IN('coupon', 'welcome', 'ad', 'ad2', 'ad_picpath1', 'ad_picpath2', 'ad_picpath3', 'link1', 'link2', 'link3', 'exp', 'pics1', 'pics2', 'scroll', 'fee1', 'fee2', 'fee3', 'logo', 'ad2_auto', 'seller_policy', 'urlA', 'urlB', 'urlC', 'urlD', 'urlE', 'urlF', 'urlG', 'urlH', 'adfee1', 'adfee2', 'cashflow', 'showimg1', 'admax1', 'imgurl0', 'imgurl1', 'imgurl2', 'imgurl3', 'imgurl8', 'ad_picpath8', 'link8')");//, 'showimg5', 'showimg6', 'showimg7', 'ad_picpath5', 'ad_picpath6', 'ad_picpath7', 'imgurl5', 'imgurl6', 'imgurl7'
$pics1 = "/images/ad_none.png";
$pics2 = "/images/ad_none.png";
$pics3 = "/images/ad_none.png";
$pics5 = "/images/ad_none.png";
$pics6 = "/images/ad_none.png";
$pics7 = "/images/ad_none.png";
$pics0 = "/images/ad_none.png";
$pics8 = "/images/ad_none.png";

while($rs = mysql_fetch_array($result)){
	JavaScript::Execute("iForm." . $rs['ID'] . ".value='" . $rs['YN'] . "';");
	switch($rs['ID']){
		case 'ad_picpath1':
			$pics1 = (($rs['YN'] != "") ? "/upload/" . basename($rs['YN']) : $pics1);
			break;
		case 'ad_picpath2':
			$pics2 = (($rs['YN'] != "") ? "/upload/" . basename($rs['YN']) : $pics2);
			break;
		case 'ad_picpath3':
			$pics3 = (($rs['YN'] != "") ? "/upload/" . basename($rs['YN']) : $pics3);
			break;
		case 'ad_picpath5':
			$pics5 = (($rs['YN'] != "") ? "/upload/" . basename($rs['YN']) : $pics5);
			break;
		case 'ad_picpath6':
			$pics6 = (($rs['YN'] != "") ? "/upload/" . basename($rs['YN']) : $pics6);
			break;
		case 'ad_picpath7':
			$pics7 = (($rs['YN'] != "") ? "/upload/" . basename($rs['YN']) : $pics7);
			break;
		case 'ad_picpath8':
			$pics8 = (($rs['YN'] != "") ? "/upload/" . basename($rs['YN']) : $pics8);
			break;
		case 'logo':
			$pics0 = (($rs['YN'] != "") ? "/upload/" . basename($rs['YN']) : $pics0);
			break;
	}

	/*
	if($rs['YN'] == "Y"){
		JavaScript::Execute("iForm." . $rs['ID'] . "[0].checked = true");
	}
	else{
		JavaScript::Execute("iForm." . $rs['ID'] . "[1].checked = true");
	}
	*/
}
include '../include/db_close.php';
JavaScript::Execute("document['pic1'].src='$pics1';");
JavaScript::Execute("document['pic2'].src='$pics2';");
JavaScript::Execute("document['pic3'].src='$pics3';");
//JavaScript::Execute("document['pic5'].src='$pics5';");
//JavaScript::Execute("document['pic6'].src='$pics6';");
//JavaScript::Execute("document['pic7'].src='$pics7';");
JavaScript::Execute("document['pic0'].src='$pics0';");
JavaScript::Execute("document['pic8'].src='$pics8';");
?>
