<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
require_once '../class/javascript.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->account_refund][1])){exit("權限不足!!");}
$no = $_REQUEST['no'];
if($no != ""){
	$result = mysql_query("SELECT *, (SELECT Member.userID FROM Member INNER JOIN Orders ON Orders.Member=Member.No WHERE Orders.ID=Billing.orderID) AS userID FROM Billing WHERE Billing.Refund=1 AND Transfer=0 AND Apply=0 AND No='$no'") or die(mysql_error());
	if($rs=mysql_fetch_array($result)){
		mysql_query("UPDATE Billing SET Transfer=1, Apply=1, dateTransfer=CURRENT_TIMESTAMP WHERE Refund=1 AND Transfer=0 AND Apply=0 AND No='$no'") or die(mysql_error());
		$sql = "INSERT INTO logTransaction(Owner, `Date`, Amount, Memo, useFor) VALUES ('" . $rs['userID'] . "', CURRENT_TIMESTAMP, '" . $rs['Total0'] . "', '退訂轉儲值', '9')";
		mysql_query($sql) or die("資料庫錯誤：" . mysql_error());
		
		//變已退款
		//Total0
//		mysql_query("UPDATE Orders SET dateRefund=CURRENT_TIMESTAMP WHERE ID='" . $rs['orderID'] . "'") or die(mysql_error());
		mysql_query("UPDATE Items SET dateRefund=CURRENT_TIMESTAMP, dateProcess=CURRENT_TIMESTAMP, dateReturn=CURRENT_TIMESTAMP WHERE orderID='" . $rs['orderID'] . "' AND Refund=1 AND No='" . $rs['itemNo'] . "'") or die(mysql_error());
//		echo $sql;
	}
	else{	
//		echo '1111';
	}
}
else{
//		echo '2222';
}
include '../include/db_close.php';
?>