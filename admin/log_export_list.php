<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->log_export][1])){exit("權限不足!!");}

$memberlist = $HTTP_POST_VARS["memberlist"];
$userid = $HTTP_POST_VARS["userid"];
if ($memberlist <> ""){
	header("Pragma: public"); 
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false); 
	header("Content-Type: application/force-download; charset=utf-8; filename=\"" . date('Y-m-d') . ".txt\";");
	header("Content-Disposition: attachment; filename=\"" . date('Y-m-d') . ".txt\";" );
	include '../include/db_open.php';
	$sql = "SELECT *, (Amount-Fee) AS Total, (SELECT Name FROM Member WHERE userID=logExport.Member) AS Name, (SELECT Phone FROM Member WHERE userID=logExport.Member) AS Phone , (SELECT SUM(Amount) FROM logTransaction WHERE Memo=logExport.ID AND useFor IN (11, 12)) AS Pay, (SELECT SUM(Amount) FROM logTransaction WHERE Owner=logExport.Member) AS Balance FROM logExport WHERE No IN ($memberlist) AND Status = 0";
	$items = array("Name", "Bank", "Branch", "Account", "Phone", "Total");
	$result = mysql_query($sql) or die (mysql_error());
	while($rs=mysql_fetch_array($result)){
		for($i=0; $i<sizeof($items); $i++){
			echo $rs[$items[$i]];
	//		if($i<sizeof($items)-1){
				echo ";";
	//		}
		}
		echo "\r\n";
	}
}
else{
    JavaScript::Alert("輸入欄位不足!!");
}
?>
