<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
require_once '../class/tools.php';
require_once '../class/system.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->search][1])){exit("權限不足!!");}
$email = $_POST['email'];
	include '../include/db_open.php';
	$result = mysql_query("SELECT * FROM Member WHERE userID = '$email'") or die (mysql_error());
	if(mysql_num_rows($result) == 0){
		echo <<<EOD
			<br><Br><br>
			<Center>
			<input type="button" value="查無會員資料，按此關閉視窗" onClick="window.close();">
			</center>
EOD;
	}
	else{
		$_SESSION['member'] = mysql_fetch_array($result);
		JavaScript::Redirect("../member.php");
	}
	include '../include/db_close.php';

?>