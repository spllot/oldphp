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

$id = $_REQUEST['id'];
$content  = $_REQUEST['content'];
$amount  = $_REQUEST['amount'];
$transfer  = $_REQUEST['transfer'];
if($id != "" && $content != "" && $amount != ""){
	include './include/db_open.php';
	$result = mysql_query("SELECT *, IFNULL((SELECT SUM(Amount) FROM Items WHERE orderID=Orders.ID AND Refund=1), 0) AS Refund, IFNULL((SELECT count(*) FROM logCertify WHERE orderID=Orders.ID AND Refund = 0 AND dateUse <> '0000-00-00 00:00:00' AND dateVertify <> '0000-00-00 00:00:00'), 0) AS Used FROM Orders WHERE ID='$id'") or die(mysql_error());
	if($orders = mysql_fetch_array($result)){
		settype($amount, "int");
		$left = $orders['Amount'] - $orders['Refund'] - $orders['Used'];
		if($left >= $amount){
			$result = mysql_query("SELECT Max(Sort) AS CURR FROM Items WHERE orderID='$id'") or die(mysql_error());
			list($curr) = mysql_fetch_row($result);
			$result = mysql_query("SELECT Certify FROM Items WHERE orderID='$id' AND Sort=0") or die(mysql_error());
			list($c) = mysql_fetch_row($result);
			
			$sql = "INSERT INTO Items set orderID='$id', Sort='" . ($curr+1) . "', Amount='$amount', Refund=1, dateRequest=CURRENT_TIMESTAMP, Reason='$content', Transfer='$transfer'";
			mysql_query($sql) or die(mysql_error());

			$sql = "UPDATE Items SET Amount = Amount - $amount WHERE orderID = '$id' AND Sort=0 AND Refund=0";
			mysql_query($sql) or die(mysql_error());

			if($orders['Deliver'] == 0){
				$result = mysql_query("SELECT * FROM logCertify WHERE Refund=0 AND orderID='$id' ORDER BY Serial DESC") or die(mysql_error());
				$i=0;
				$certify = "";
				while($rs = mysql_fetch_array($result)){
					$i++;
					mysql_query("UPDATE logCertify SET Refund = 1 WHERE No='" . $rs['No'] . "' AND Refund=0") or die(mysql_error());
					$tmp = "";
					if($certify != ""){
						$tmp = ",";
					}
					$certify = $rs['Serial'] . $tmp . $certify;
					if($i >= $amount){
						break;
					}
				}
				$sql = "UPDATE Items SET Certify='" . str_replace($certify, "", $c) . "' WHERE orderID = '$id' AND Sort=0 AND Refund=0";
				mysql_query($sql) or die(mysql_error());
				$sql = "UPDATE Items SET Certify='" . $certify . "' WHERE orderID = '$id' AND Sort='" . ($curr+1) . "' AND Refund=1";
				mysql_query($sql) or die(mysql_error());
			}

			JavaScript::Alert("退訂申請已送出，我們會盡快為您處理!");
		}
		else{
			JavaScript::Alert("退訂數量有誤，請重新整理頁面!");
		}
		JavaScript::Execute("window.parent.Close()");
		JavaScript::Execute("window.parent.location.reload()");
	}
	include './include/db_close.php';
}
?>
