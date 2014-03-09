<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
require_once '../class/javascript.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->account_refund][1])){exit("權限不足!!");}
$tab = $_REQUEST['tab'];
$Y = $_REQUEST['Y'];
$M = $_REQUEST['M'];
$D = $_REQUEST['D'];
JavaScript::setCharset("UTF-8");
$payby = array("", "信用卡", "", "ATM");
if($tab == 23 && $Y != "" && $M != "" && $D != ""){

	$result = mysql_query("SELECT * FROM logBilling WHERE Y='$Y' AND M='$M' AND D='$D' AND Refund=1") or die(mysql_error());
	if(mysql_num_rows($result) == 0){
//退訂中
//
//
//---------------ver.1
		$sql = "(SELECT Orders.ID FROM logCertify INNER JOIN Orders ON logCertify.orderID = Orders.ID WHERE Orders.Status = 1 AND logCertify.Refund=1)
		UNION
		(SELECT Orders.ID FROM Items INNER JOIN Orders ON Items.orderID=Orders.ID WHERE Orders.Status = 1 AND Items.Refund = 1)
		UNION
		(SELECT Orders.ID FROM Items INNER JOIN Orders ON Items.orderID=Orders.ID WHERE Items.Refund = 1 AND Transfer=0)";

		$result = mysql_query($sql) or die(mysql_error());
		$num = mysql_num_rows($result);
		$ids = "";
		$i=0;
		while($rs=mysql_fetch_array($result)){
			$ids .= "'" . $rs['ID'] . "'";
			$ids .= (($i<$num-1) ? ",":"");
			$i++;
		}

//----------------ver.2

		$sql = "SELECT * FROM Orders WHERE 
		(
			(dateShipped<>'0000-00-00 00:00:00' AND datediff(Now(), dateShipped) > 10)
			OR
			(dateShipped='0000-00-00 00:00:00' AND datediff(Now(), dateSubmited) > 10)
		)
		AND Orders.ID NOT IN (SELECT orderID FROM Billing WHERE Apply=1 AND Refund=1)";

		$result = mysql_query($sql) or die(mysql_error());
		$ids = "";
		while($rs=mysql_fetch_array($result)){
			$ids .= "'" . $rs['ID'] . "',";
		}

//		mysql_query("INSERT INTO logBilling SET Y='$Y', M='$M', D='$D', Refund=1, dateGenerate=CURRENT_TIMESTAMP") or die(mysql_error());
		$no = mysql_insert_id();
		if($ids != ""){	
			$ids = substr($ids, 0, strlen($ids) - 1);
			$sql = "SELECT Orders.Price, Orders.Product, Orders.Seller, Orders.Amount, Orders.ID AS OID, Payment.ID AS PID, Orders.pName, Orders.Deliver, Orders.dateShipped, Orders.Status, Orders.Price, Payment.payBy , IFNULL((SELECT payBy FROM Payment WHERE Memo=Orders.ID AND payBy <> 4), 0) as payBy1 FROM Orders INNER JOIN Payment ON Orders.ID = Payment.Memo WHERE Orders.ID IN ($ids) ORDER BY Payment.ID DESC";
			$result = mysql_query($sql) or die(mysql_error());
			while($rs = mysql_fetch_array($result)){
				$paid = 0;
				$prepaid = 0;
				$total = 0;
				$fee = 0;
				$num = $rs['Amount'];
	/**/

				$result1 = mysql_query("SELECT Amount FROM Payment WHERE Memo='" . $rs['OID'] . "' AND payBy = 4") or die(mysql_error());
				if(mysql_num_rows($result1) > 0){list($prepaid) = mysql_fetch_row($result1);}
				$result1 = mysql_query("SELECT Amount FROM Payment WHERE Memo='" . $rs['OID'] . "' AND payBy <> 4") or die(mysql_error());
				if(mysql_num_rows($result1) > 0){list($paid) = mysql_fetch_row($result1);}
				
				$result1 = mysql_query("SELECT * FROM Items WHERE Refund=1 AND orderID='" . $rs['OID'] . "'") or die(mysql_error());
				while($rs1 = mysql_fetch_array($result1)){
					$reason = "";
					$refund = 0; //儲值金退款
					$total = 0; //其它退款
					$num = 0;
					$refund1 = 0;
					$refund2 = 0;
					$itemno = "";
					$num = $rs1['Amount'];
					$refund1 = $rs['Price'] * $rs1['Amount'];
					$itemno .= $rs1['No'];
					if($rs['payBy'] == 3 && $rs1['Transfer'] == 1){
						$reason = $payby[$rs['payBy']] . "退款";
					}

					if($paid >= $refund1){
						$total = $refund1;
						$refund1 = 0;
					}
					else{
						$total = $paid;
						$refund1 -= $paid;
					}
					
					if($refund1 > 0){
						$refund = $refund1;
					}
					
					if($reason != ""){
						$refund += $total;
						$total = 0;
					}
		//			$total = $refund2;
		//			$refund = $refund1;

					/*
					if($rs['Deliver'] == 1){
					}
					else{
					}
					*/

					$ratio = 0;
					switch($rs['payBy1']){
						case 1:
							$ratio = 0;
							break;
						case 3:
							$ratio = 0.005;
							break;
					}
					//$amount 已付
					//$prepaid 已付儲值金
		//			echo $rs['payBy'] . "==>" . $ratio . "<br>";
					$fee = ceil($total * $ratio);//手續費
					$total = $total - $fee;//退款額

					
					echo <<<EOD
<pre style="display:none">
=========================================================================
退量：{$num}
售價：{$rs['Price']}
轉儲值：{$reason}
金流付款：{$paid}
儲值付款：{$prepaid}
金流退款：{$total}
儲值退款：{$refund}
</pre>
EOD;
					if($num > 0){
						$sql = "INSERT INTO Billing SET Price='" . $rs['Price'] . "', Product='" . $rs['Product'] . "', Seller='" . $rs['Seller'] . "', paymentID='" . $rs['PID'] . "', orderID='" . $rs['OID'] . "', Member='', Phone='', payBy='" . $rs['payBy'] . "', Num='$num', Name='" . $rs['pName'] . "', Amount='$paid', Prepaid='$prepaid', Total0='$refund', Total='$total', Fee='$fee', Refund=1, logNo='$no', Reason='$reason', itemNo='$itemno'";
						echo $sql . "<br>";
	//					mysql_query($sql) or die(mysql_error());
					}
			
				}
			}
		}
	}
	else{
		JavaScript::Alert("對帳單已存在!");
	}
}
else{
	JavaScript::Alert("輸入欄位不足!");
}
include '../include/db_close.php';
//JavaScript::Redirect("account_refund.php?tab=$tab&Y=$Y&M=$M&D=$D");
?>