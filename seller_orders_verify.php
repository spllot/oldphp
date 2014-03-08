<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	exit;
}
$i=$_REQUEST['i'];
$r=$_REQUEST['r'];
$k=$_REQUEST['k'];
$s=$_REQUEST['s'];
$product=$_REQUEST['p'];
include './include/db_open.php';
$result = mysql_query("SELECT * FROM logCertify WHERE orderID='$i' AND Seller='" . $_SESSION['member']['No'] . "' AND Sort='$s'") or die(mysql_error());
$feedback['verify']=0;
if($certify=mysql_fetch_array($result)){
	if($k != ""){
		if(strcasecmp($k, substr($certify['Serial'], 7, 10)) == 0){
			mysql_query("UPDATE logCertify SET Keypass='" . strtoupper($k) . "', dateVertify=CURRENT_TIMESTAMP, dateUse=CURRENT_TIMESTAMP WHERE No='" . $certify['No'] . "' AND Refund=0 AND Expire=0");
			$feedback['verify']=1;

			$sql = "SELECT ifnull(COUNT(logCertify.Serial), 0) FROM Orders INNER JOIN logCertify ON logCertify.orderID=Orders.ID WHERE Orders.Status = 1 AND Deliver=0 AND  Orders.Seller = '" . $_SESSION['member']['No'] . "' AND Orders.Status = 1 AND logCertify.dateUse <> '0000-00-00 00:00:00' AND logCertify.Refund = 0" . (($product != "") ? " AND Orders.Product='$product'" : "");
			
			$result = mysql_query($sql) or die(mysql_error());
			if($rs = mysql_fetch_array($result))
				$feedback['counts'] = $rs[0];
		}
		else{
			$feedback['verify']=2;
		}
	}
	if($r != ""){
		mysql_query("UPDATE logCertify SET  dateReserve = '$r' WHERE No='" . $certify['No'] . "' AND Refund=0");
	}
}
echo json_encode($feedback);
include './include/db_close.php';
?>
