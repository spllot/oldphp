<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
include("../class/tools.php");
require_once '../class/system.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->blog][1])){exit("權限不足!!");}
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
echo "<script language=\"javascript\">\n";
$curr = $_REQUEST['curr'];
$tab = $_REQUEST['tab'];
$no = $_REQUEST['no'];
$S1 = $_REQUEST['S1'];
$S2 = $_REQUEST['S2'];
$S3 = $_REQUEST['S3'];
$S4 = $_REQUEST['S4'];
$S5 = $_REQUEST['S5'];
include("../include/db_open.php");
$result = mysql_query("SELECT * FROM Config WHERE ID='{$curr}S'");
if($rs = mysql_fetch_array($result)){
	$price = $rs['YN'];
}


$result = mysql_query("SELECT * FROM Blog WHERE No = '$no'");
if($blog = mysql_fetch_array($result)){
	if($blog['dateConfirmed'] =='0000-00-00 00:00:00'){
		$earn = $S1 + $S2 + $S3 + $S4 + $S5;
		
		if($blog['toStock'] == 1){
			$amount = round($earn * 3 / $price);
			$sql = "INSERT INTO logStock SET Owner='" . $blog['userID'] . "', `Date`=CURRENT_TIMESTAMP, Amount='$amount', Memo='" . date('Y-m') . "部落格行銷得分入股{$price}'";
			mysql_query($sql);
			$tno = mysql_insert_id();
		}
		else{
			$sql = "INSERT INTO logTransaction SET Owner='" . $blog['userID'] . "', `Date`=CURRENT_TIMESTAMP, Amount='$earn', Memo='" . date('Y-m') . "部落格行銷得分', useFor=6";
			mysql_query($sql);
			$tno = mysql_insert_id();
		}
		mysql_query("UPDATE Blog SET S1 = '$S1', S2='$S2', S3 = '$S3', S4 = '$S4', S5 = '$S5', Earn='$earn', dateConfirmed=CURRENT_TIMESTAMP, Status = 1, transactionNo='$tno' WHERE No = '$no'");
	}
	else{
		echo "alert('該部落格文章已評分!');";
	}
}
else{
	echo "alert('找不到該部落格文章!');";
}
include("../include/db_close.php");
echo "window.location.href=\"blog.php?tab=$tab\";\n";
echo "</script>\n";

?>
