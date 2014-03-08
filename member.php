<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Redirect("./");
	exit;
}

include 'layout2.php';

$url = $_REQUEST['url'];

//echo "<script language='javascript'>iAcontent.location.href='$url';</script>";
$menu = (($_REQUEST['menu'] == "") ? "1" : $_REQUEST['menu']);
echo '<script language="javascript">document.getElementById("m' . $menu . '").click();</script>';
if($url != ""){
	echo "<script language='javascript'>iAcontent.location.href='$url';</script>";
}
?>