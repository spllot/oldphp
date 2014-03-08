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
$result = mysql_query("SELECT * FROM Blog WHERE No = '$no'");
if($blog = mysql_fetch_array($result)){
	if($blog['dateConfirmed'] =='0000-00-00 00:00:00'){
		$earn = $S1 + $S2 + $S3 + $S4 + $S5;
		mysql_query("INSERT INTO logTransaction SET Owner='" . $blog['userID'] . "', `Date`=CURRENT_TIMESTAMP, Amount='$earn', Memo='" . date('Y-m') . "部落格行銷得分', useFor=6");

		$tno = mysql_insert_id();
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
