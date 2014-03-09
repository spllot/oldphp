<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->seller_export][1])){exit("權限不足!!");}
$no = $_REQUEST['no'];
$t = $_REQUEST['t'];
$f = $_REQUEST['f'];
$Y = $_REQUEST['Y'];
$M = $_REQUEST['M'];
$postage = $_REQUEST['p'];
$fee = $_REQUEST['fee'];
include("../include/db_open.php");

$result = mysql_query("SELECT * FROM sellerExport WHERE Seller='$no' AND Y='$Y' AND M='$M'") or die(mysql_error());
if(mysql_num_rows($result) == 0){
	mysql_query("INSERT INTO sellerExport SET Y='$Y', M='$M', Seller='$no', Total='$t', Earn='$f', Postage='$postage', Fee='$fee', dateRequest=CURRENT_TIMESTAMP ") or die(mysql_error());
	$result = mysql_query("SELECT Billing.No FROM Billing INNER JOIN logBilling WHERE  Y='$Y' AND M='$M' AND Billing.Seller='$no' AND Billing.Apply=1 AND Billing.Refund=0") or die(mysql_error());
	while($rs=mysql_fetch_array($result)){
		mysql_query("UPDATE Billing SET dateExport = CURRENT_TIMESTAMP WHERE No='" . $rs['No'] . "'") or die(mysql_error());
	}
}
include '../include/db_close.php';
?> 