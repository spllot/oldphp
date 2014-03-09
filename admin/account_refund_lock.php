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
if($tab == 23 && $Y != "" && $M != "" && $D != ""){
	$result = mysql_query("SELECT * FROM logBilling WHERE Y='$Y' AND M='$M' AND D='$D' AND Refund=1") or die(mysql_error());
	if($rs=mysql_fetch_array($result)){
		if($rs['Lock'] == 0){
			mysql_query("UPDATE logBilling SET `Lock`=1 WHERE Y='$Y' AND M='$M' AND D='$D' AND Refund=1") or die(mysql_error());
			$apply = $_REQUEST['apply'];
			for($i=0; $i<sizeof($apply); $i++){
				if($apply[$i] != ""){
					$result = mysql_query("SELECT *, (SELECT Member.userID FROM Member INNER JOIN Orders ON Orders.Member=Member.No WHERE Orders.ID=Billing.orderID) AS userID FROM Billing WHERE Billing.Refund=1 AND Transfer=0 AND Apply=0 AND No='" . $apply[$i] . "'") or die(mysql_error());
					if($rs=mysql_fetch_array($result)){
						if($rs['Total0'] > 0){
							$today = date("Y-m-d H:i:s");
							$date_active = date('Y-m-d H:i:s', strtotime($totay . " +3 day"));
							mysql_query("INSERT INTO queueTransfer SET Billing='" . $apply[$i] . "', Amount='" . $rs['Total0'] . "', dateAdd=CURRENT_TIMESTAMP, dateActive='$date_active', userID='" . $rs['userID'] . "', Memo='" . $rs['orderID'] . "'") or die(mysql_error());
						}
					}
					mysql_query("UPDATE Billing SET Apply = 1 WHERE No='" . $apply[$i] . "'") or die(mysql_error());
				}
			}
		}
		else{
			$lock = $_REQUEST['lock'];
			for($i=0; $i<sizeof($lock); $i++){
				if($lock[$i] != ""){
					mysql_query("UPDATE Billing SET Apply = 0 WHERE No='" . $lock[$i] . "'") or die(mysql_error());
					mysql_query("DELETE FROM queueTransfer WHERE Billing='" . $lock[$i] . "'") or die(mysql_error());
				}
			}
		}
	}
}
else{
	JavaScript::Alert("輸入欄位不足!");
}
include '../include/db_close.php';
JavaScript::Redirect("account_refund.php?tab=$tab&Y=$Y&M=$M&D=$D");
?>