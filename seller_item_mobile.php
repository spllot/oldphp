<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	exit;
}

$mobile = $_REQUEST['mobile'];
$no = $_REQUEST['no'];

JavaScript::setCharset("UTF-8");
if($no != ""){
	if(!empty($_SESSION['member'])){
		include './include/db_open.php';
		$sql = "UPDATE Product SET mobile='$mobile' WHERE No='$no' AND Member='" . $_SESSION['member']['No'] . "'";
		mysql_query($sql) or die (mysql_error());
		include './include/db_close.php';
	}
}
?>
