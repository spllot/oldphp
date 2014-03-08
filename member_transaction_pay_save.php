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

exit;
$amount = $_REQUEST['amount'];
$phone = $_REQUEST['phone'];
$payby = $_REQUEST['payby'];
$agree = $_REQUEST['agree'];

if($amount != "" && $phone != "" && $payby != "" && $agree != ""){
	include './include/db_open.php';
	$fee = 0;
	$complete = 0;
	$expire_date="";
	switch($payby){
		case 1:
			$fee = ceil($amount * 0.03);
			$select_paymethod=1;
			break;
		case 2:
			$fee = ceil($amount * 0.02);
			$select_paymethod=4;
			break;
		case 3:
			$fee = ceil($amount * 0.005);
			$select_paymethod=2;
			$complete = 1;
			$expire_date = date('Y-m-d', strtotime(date('Y-m-d H:i:s') . "+24 hour"));
			break;
	}

	$prefix = "P" . date('ymd');
	$result = mysql_query("SELECT ID FROM Payment WHERE ID LIKE '%$prefix%' ORDER BY ID DESC");
	if(mysql_num_rows($result) >0){
		list($curr_id)=mysql_fetch_row($result);
		$curr_no = (int)substr($curr_id, -4);
		
		$id = $prefix . substr("000" . ($curr_no + 1), -4);
	}
	else{
		$id = $prefix . "0001";
	}

	$sql = "insert into Payment(ID, Member, Phone, payBy, Amount, Fee, dateSubmited, Complete, Memo) VALUES('$id', '" . $_SESSION['member']['userID'] . "', '$phone', '$payby', '$amount', '$fee', CURRENT_TIMESTAMP, '$complete', '$memo')";
	echo $sql;
	mysql_query($sql) or die (mysql_error());





	$mid = "3231";
	$txid = $id;
	$msg = $v1 . "|" . $mid . "|" . $txid . "|" . $amount . "|" . $v2;
	$verify = md5($msg);
	$cemail = $_SESSION['member']['userID'];
	$cname = $_SESSION['member']['Name'];
	$caddress = $_SESSION['member']['Address'];
	$ctel = $_SESSION['member']['Phone'];
	$amount += $fee;
	$v1 = "030302f0a32277e1244b5dd15bd9ad5b";
	$v2 = "a5b3b9c9650e8bda2d143794e183e49e";
	$msg = $v1 . "|" . $mid . "|" . $txid . "|" . $amount . "|" . $v2;
	$verify = md5($msg);


	JavaScript::Alert("申請已送出，系統將導到台灣里付款系統，請依網頁指示進行付款動作!");
	JavaScript::Execute("parent.parent.openPay('$mid', '$txid', '$amount', '$verify', '$cname', '$caddress', '$ctel', '$cemail', '$select_paymethod', '$expire_date')");
//	JavaScript::setURL("member_transaction.php?y=$y&m=$m" ,"window.parent");
	include './include/db_close.php';
}
?>
