<?php
include '../include/auth_admin.php';
require_once '../class/admin2.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
require_once '../class/javascript.php';
include("../include/db_open.php");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->account_income][1])){exit("權限不足!!");}
$tab = $_REQUEST['tab'];
$Y = $_REQUEST['Y'];
$M = $_REQUEST['M'];
$D = $_REQUEST['D'];
JavaScript::setCharset("UTF-8");
if($tab == 23 && $Y != "" && $M != "" && $D != ""){

	$result = mysql_query("SELECT * FROM logBilling WHERE Y='$Y' AND M='$M' AND D='$D' AND Refund=0") or die(mysql_error());
	if(mysql_num_rows($result) == 0){

		//到店 已完成 待消費
		//宅配 已完成
		//退款轉儲值金
//--------------ver.1
/*
		$sql = "(SELECT Orders.ID FROM logCertify INNER JOIN Orders ON logCertify.orderID = Orders.ID WHERE Orders.Status = 1 AND dateShipped<>'0000-00-00 00:00:00' AND (logCertify.dateUse <> '0000-00-00 00:00:00' OR (logCertify.dateUse = '0000-00-00 00:00:00' AND datediff(Now(), dateShipped) > 10)))
		UNION
		(SELECT Orders.ID FROM Items INNER JOIN Orders ON Items.orderID=Orders.ID WHERE Orders.Status = 1 AND dateShipped<>'0000-00-00 00:00:00' AND datediff(Now(), dateShipped) > 10 AND Items.Refund = 0)
		UNION
		(SELECT Orders.ID FROM Items INNER JOIN Orders ON Items.orderID=Orders.ID WHERE Items.Refund = 1 AND Transfer=1)";

		$result = mysql_query($sql) or die(mysql_error());
		$num = mysql_num_rows($result);
		$ids = "";
		$i=0;
		while($rs=mysql_fetch_array($result)){
			$ids .= "'" . $rs['ID'] . "'";
			$ids .= (($i<$num-1) ? ",":"");
			$i++;
		}
*/
//---------------ver.2


		$sql = "SELECT * FROM Orders WHERE 
		(
			(dateShipped<>'0000-00-00 00:00:00' AND datediff(Now(), dateShipped) > 10)
			OR
			(dateShipped='0000-00-00 00:00:00' AND datediff(Now(), dateSubmited) > 10)
		)
		AND Orders.ID NOT IN (SELECT orderID FROM Billing WHERE Apply=1 AND Refund=0)";


//echo $sql . "<br>";
		$result = mysql_query($sql) or die(mysql_error());
		$ids = "";
		while($rs=mysql_fetch_array($result)){
			$ids .= "'" . $rs['ID'] . "',";
		}
		
		mysql_query("INSERT INTO logBilling SET Y='$Y', M='$M', D='$D', Refund=0, dateGenerate=CURRENT_TIMESTAMP") or die(mysql_error());
		$no = mysql_insert_id();

		if($ids != ""){		
			$ids = substr($ids, 0, strlen($ids) - 1);
			$sql = "SELECT Orders.Price, Orders.Product, Orders.Seller, Orders.Amount, Orders.ID AS OID, Payment.ID AS PID, Orders.pName, Orders.Deliver, Orders.dateShipped, Orders.Status, Orders.Price, IFNULL((SELECT payBy FROM Payment WHERE Memo=Orders.ID AND payBy<>4), 0) AS payBy FROM Orders INNER JOIN Payment ON Orders.ID = Payment.Memo WHERE Orders.ID IN ($ids) ORDER BY Payment.ID DESC";
			$result = mysql_query($sql) or die(mysql_error());
			while($rs = mysql_fetch_array($result)){
				$amount = 0;
				$prepaid = 0;
				$total = 0;
				$fee = 0;
				$num = $rs['Amount'];

				$result1 = mysql_query("SELECT Amount FROM Payment WHERE Memo='" . $rs['OID'] . "' AND payBy = 4") or die(mysql_error());
				list($prepaid) = mysql_fetch_row($result1);
				$result1 = mysql_query("SELECT Amount FROM Payment WHERE Memo='" . $rs['OID'] . "' AND payBy <> 4") or die(mysql_error());
				list($amount) = mysql_fetch_row($result1);

				$result1 = mysql_query("SELECT * FROM Items WHERE orderID='" . $rs['OID'] . "' AND ((Refund=1 AND Transfer=1) OR Refund=0)") or die(mysql_error());
				$reason = "";

				$money=0;
				$num = 0;
				while($rs1 = mysql_fetch_array($result1)){
					$money += $rs['Price'] * $rs1['Amount'];
					$num += $rs1['Amount'];
					$itemno .= $rs1['No'];
					if($rs['payBy'] == 3 && $rs1['Transfer'] == 1){
						$reason .= "ATM退轉儲值({$rs1['Amount']}) ";
					}
					else{
						$reason .= "待消費/已完成({$rs1['Amount']}) ";
						/*
						if($rs['Deliver'] == 0){
							if($rs1['dateUse'] == '0000-00-00 00-00-00'){
								$reason .= "待消費({$rs1['Amount']}) ";
							}
							else{
								$reason .= "已完成({$rs1['Amount']}) ";
							}
						}
						else{
							$reason .= "已完成({$rs1['Amount']}) ";
						}
						*/
					}
				}


				if($rs['Deliver'] == 1){
				}
				else{
				}

				$ratio = 0;
				switch($rs['payBy']){
					case 1:
						$ratio = 0.03;
						break;
					case 3:
						$ratio = 0.005;
						break;
				}
				$fee = ceil($amount * $ratio);
				$total = $amount - $fee;
				if($num > 0){
					$rst1=mysql_query("SELECT * FROM Billing WHERE orderID='".$rs['OID']. "' AND logNo='$no'") or die(mysql_error());
					if(mysql_num_rows($rst1) == 0){
						$deliver = $rs['Deliver'];
						$id=$rs['OID'];

						if($deliver==0){
							$sql = "(SELECT Orders.Price, Orders.dateSubmited, Orders.Deliver, Orders.ID, Orders.Status, Orders.pName, Orders.dateShipped, logCertify.Serial, logCertify.Refund, logCertify.dateVertify, logCertify.dateUse, '0000-00-00 00:00:00' as dateReturn, 1 AS Amount, '0000-00-00 00:00:00' AS dateRefund, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), '0') AS A1, IFNULL((SELECT payBy FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), 0) AS P1, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 0) AS A2, IFNULL((SELECT payBy FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 4) AS P2, 0 AS Transfer, logCertify.Expire FROM Orders INNER JOIN logCertify ON Orders.ID = logCertify.orderID WHERE logCertify.Refund=0 AND Deliver=$deliver AND Orders.ID='$id')
							UNION ALL
							(SELECT Orders.Price, Orders.dateSubmited, Orders.Deliver, Orders.ID, Orders.Status, Orders.pName, Orders.dateShipped, '' as Serial, Items.Refund, '0000-00-00 00:00:00' as dateVertify, '0000-00-00 00:00:00' as dateUse, Items.dateReturn, Items.Amount, Items.dateRefund, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), '0') AS A1, IFNULL((SELECT payBy FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), 0) AS P1, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 0) AS A2, IFNULL((SELECT payBy FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 4) AS P2, Items.Transfer, Items.Expire FROM Orders INNER JOIN Items ON Orders.ID = Items.orderID WHERE Items.Refund=1 AND Deliver=$deliver AND Orders.ID='$id')
							order by dateSubmited DESC, Refund, Serial";
						}
						else{
							$sql = "SELECT Orders.*, Items.Amount AS Amount2, Items.Refund, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), '0') AS A1, IFNULL((SELECT payBy FROM Payment WHERE payBy<>4 AND Memo=Orders.ID), 0) AS P1, IFNULL((SELECT Amount + Fee FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 0) AS A2, IFNULL((SELECT payBy FROM Payment WHERE payBy=4 AND Memo=Orders.ID), 4) AS P2, Items.Transfer, Items.Expire FROM Orders INNER JOIN Items ON Orders.ID=Items.orderID WHERE Deliver=$deliver AND Items.Amount > 0 AND Orders.ID='$id'";
						}


						$rest2 = mysql_query($sql) or die(mysql_error());
						$now = date('Y-m-d H:i:s');
						$aaa['Refunds']=0;
						$aaa['Completes']=0;
						$aaa['Waitings']=0;
						$aaa['Refunds']=0;
						while($data=mysql_fetch_array($rest2)){
							switch($data['Status']){//0.未付款, 1.已付款, 2.退訂, 3.取消
								case 0:
									$status = "待付款";
									break;
								case 1:
									if($data['Refund'] == 1){
										$status = "退訂中";
										if($data['dateRefund'] != "0000-00-00 00:00:00"){
											$status = "已退款";
											$aaa['Refunds'] += $data['Amount'];
										}
										$status .= (($data['Transfer'] == 1) ? "<br>(" . (($data['Expire'] == 1) ? "到期":"") . "轉儲值)" :"");
									}
									else{
										if($data['Deliver'] == 1){
											if($data['dateShipped'] == "0000-00-00 00:00:00"){
												$status = "待發貨";
												$service .= "<br><a href='javascript:parent.Dialog(\"orders_refund.php?id={$data['ID']}\");'>退訂申請</a>";
											}
											else{
												$trial = date("Y-m-d", strtotime($data['dateShipped'] . " +10 days")) . "23:59:59";
												if($now > $trial){
													$status = "已完成";
													$aaa['Completes'] += $data['Amount'];
												}
												else{
													$status = "待鑑賞";
													$service .= "<br><a href='javascript:parent.Dialog(\"orders_refund.php?id={$data['ID']}\");'>退訂申請</a>";
												}
											}
										}
										else{
											if($data['dateShipped'] == "0000-00-00 00:00:00"){
												$status = "待鑑賞";
											}
											else{
												$trial = date("Y-m-d", strtotime($data['dateShipped'] . " +7 days")) . "23:59:59";
												if($now > $trial){
													$status = "待消費";
													$aaa['Waitings'] += $data['Amount'];
												}
												else{
													$status = "待鑑賞";
													$service .= "<br><a href='javascript:parent.Dialog(\"orders_refund.php?id={$data['ID']}\");'>退訂申請</a>";
												}
											}
											if($data['dateUse'] != '0000-00-00 00:00:00'){
												$status = "已完成";
												$aaa['Completes'] += $data['Amount'];
											}
										}
									}
									break;
								case 2:
									$status = "退訂中";
									if($data['dateRefund'] != "0000-00-00 00:00:00"){
										$status = "已退款";
										$aaa['Refunds'] += $data['Amount'];
									}
									$status .= (($data['Transfer'] == 1) ? "<br>(" . (($data['Expire'] == 1) ? "到期":"") . "轉儲值)" :"");
									break;
								case 3:
									$status = "已取消";
									$aaa['Cancels'] += $data['Amount'];
									break;
							}
							//echo $id . $status . $data['Amount'];
						}
						//echo $id . "<br>";
						$sql= "INSERT INTO Billing SET Price='" . $rs['Price'] . "', Product='" . $rs['Product'] . "', Seller='" . $rs['Seller'] . "', paymentID='" . $rs['PID'] . "', orderID='" . $rs['OID'] . "', Member='', Phone='', payBy='" . $rs['payBy'] . "', Num='$num', Name='" . $rs['pName'] . "', Amount='$amount', Prepaid='$prepaid', Total='$total', Fee='$fee', Refund=0, logNo='$no', Reason='$reason', itemNo='$itemno', Amounts='" . $rs['Amount'] . "', Cancels='" . $aaa['Cancels'] . "', Refunds='" . $aaa['Refunds'] . "', Completes='" . $aaa['Completes'] . "', Waitings='" . $aaa['Waitings'] . "', Dones='" . $aaa['Dones'] . "'";
						mysql_query($sql) or die(mysql_error());
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
JavaScript::Redirect("account_income.php?tab=$tab&Y=$Y&M=$M&D=$D");
?>