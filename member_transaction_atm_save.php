<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
JavaScript::setCharset("UTF-8");
if(empty($_SESSION['member'])){
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}


$amount = $_REQUEST['amount'];
$account = $_REQUEST['account'];
$bank = $_REQUEST['bank'];
$date = $_REQUEST['date'];
include './include/db_open.php';
if($bank != "" && $date != "" && $amount != "" && $account != ""){
	$sql = "INSERT INTO logATM SET dateLog=CURRENT_TIMESTAMP, Amount='$amount', Bank='$bank', Account='$account', dateTrans='$date', Member='" . $_SESSION['member']['No'] . "', dateUpdate=CURRENT_TIMESTAMP";
	mysql_query($sql) or die(mysql_error());
	JavaScript::Alert("匯款回報已送出，我們會盡快為您處理!");
	JavaScript::setURL("member_transaction_atm.php" ,"window.parent");
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
include './include/db_close.php';
?>
