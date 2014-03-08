<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	exit;
}

$event_date = $_REQUEST['event_date'];
$event_start = $_REQUEST['event_start'];
$event_end = $_REQUEST['event_end'];
$no = $_REQUEST['no'];

JavaScript::setCharset("UTF-8");
if($no != "" && $event_date !="" && $event_start != "" && $event_end && $event_end > $event_start){
	if(!empty($_SESSION['member'])){
		include './include/db_open.php';
		$sql = "UPDATE Product SET event_date='$event_date', event_start='$event_start', event_end='$event_end' WHERE No='$no' AND Member='" . $_SESSION['member']['No'] . "'";
		mysql_query($sql) or die (mysql_error());
		include './include/db_close.php';
	}
}
?>
