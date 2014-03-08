<?php
include './include/session.php';
if(empty($_SESSION['member'])){exit;}
else if($_SESSION['member']['Seller'] != 2){exit;}
$_SESSION['cashflow'] = $_REQUEST['cashflow'];
if($_SESSION['cashflow'] != "1"){
	$limit = array(0, 4, 4, 8, 8, 16, 16, 32, 32);
	include './include/db_open.php';
	$sql = "SELECT COUNT(*) FROM Product WHERE dateClose > CURRENT_TIMESTAMP AND dateApprove <> '0000-00-00 00:00:00' AND Member='" . $_SESSION['member']['No'] . "' AND Cashflow=0";
	$result = mysql_query($sql) or die(mysql_error());
	list($counts1) = mysql_fetch_row($result);
	$sql = "SELECT COUNT(*) FROM Product WHERE Status = 1 AND dateApprove = '0000-00-00 00:00:00' AND Member='" . $_SESSION['member']['No'] . "' AND Cashflow=0";
	$result = mysql_query($sql) or die(mysql_error());
	list($counts2) = mysql_fetch_row($result);
	$counts = $counts1+$counts2;
	
	if($_SESSION['member']['VIP'] == 0){
		if($limit[$_SESSION['member']['Level']] <= $counts){
			echo "您的等級 {$_SESSION['member']['Level']} 只能提案 {$limit[$_SESSION['member']['Level']]} 個非金流商品(上架{$counts1}, 審核中{$counts2})!";
		}
	}
	include './include/db_close.php';
}
?>