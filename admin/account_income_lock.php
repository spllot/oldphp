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
	if($rs=mysql_fetch_array($result)){
		if($rs['Lock'] == 0){
			mysql_query("UPDATE logBilling SET `Lock`=1 WHERE Y='$Y' AND M='$M' AND D='$D' AND Refund=0") or die(mysql_error());
			$apply = $_REQUEST['apply'];
			for($i=0; $i<sizeof($apply); $i++){
				if($apply[$i] != ""){
					mysql_query("UPDATE Billing SET Apply = 1 WHERE No='" . $apply[$i] . "'") or die(mysql_error());
				}
			}
		}
		else{
			$lock = $_REQUEST['lock'];
			for($i=0; $i<sizeof($lock); $i++){
				if($lock[$i] != ""){
					mysql_query("UPDATE Billing SET Apply = 0 WHERE No='" . $lock[$i] . "'") or die(mysql_error());
				}
			}
		}
	}
}
else{
	JavaScript::Alert("輸入欄位不足!");
}
include '../include/db_close.php';
JavaScript::Redirect("account_income.php?tab=$tab&Y=$Y&M=$M&D=$D");
?>