<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->export][1])){exit("權限不足!!");}
$status = $_REQUEST["status"];
$level = $_REQUEST["level"];
$seller = $_REQUEST["seller"];
$to = $_REQUEST["to"];

$sql = "SELECT * FROM Member WHERE 1=1 ";
$sql .= (($status != "") ? " AND Status = '$status'" : "");
$sql .= (($level != "") ? " AND Level = '$level'" : "");
$sql .= (($seller != "") ? " AND Seller = '$seller'" : "");

switch($to){
	case 1:
		$sql .= " AND 1=1";
		break;
	case 2:
		$sql .= " AND Seller<>2";
		break;
	case 3:
		$sql .= " AND Seller=2";
		break;
	case 4:
		$sql .= " AND Subscribe=1";
		break;
	case 5:
		$sql =  "SELECT DISTINCT EMail AS userID, EMail as Name FROM Subscribe";
		break;
}



/*print_r($_REQUEST);
*/

header("Pragma: public"); 
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false); 
header("Content-Type: application/force-download; charset=utf-8; filename=\"" . date('Y-m-d') . ".txt\";");
header("Content-Disposition: attachment; filename=\"" . date('Y-m-d') . ".txt\";" );
include '../include/db_open.php';
$result = mysql_query($sql) or die (mysql_error());
$items = explode(",", $_REQUEST['itemlist']);
$email = $_REQUEST['email'];

if($email == "1"){
	while($rs=mysql_fetch_array($result)){
		echo $rs['Name'] . "<" . $rs['userID'] . ">;";
	}
}
else{
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
?>
