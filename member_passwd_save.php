<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}


$pass1 = $_REQUEST['pass1'];
$pass2 = $_REQUEST['pass2'];
$pass3 = $_REQUEST['pass3'];
JavaScript::setCharset("UTF-8");
if($pass1 != "" && $pass2 != "" && ($pass2==$pass3)){
	if(!Tools::checkPassword($pass2)){JavaScript::Alert("新密碼長度必須為6-12個字!!");exit;}
	include './include/db_open.php';
	$sql = "SELECT * FROM Member WHERE userID = '" . $_SESSION['member']['userID'] . "' AND userPass = binary'$pass1'";
	$result = mysql_query($sql) or die (mysql_error());
	if(mysql_num_rows($result) == 0){
		JavaScript::Alert("舊密碼錯誤!!");
	}
	else{	
		$sql = "UPDATE Member SET userPass='$pass2', dateUpdate=CURRENT_TIMESTAMP WHERE userID = '" . $_SESSION['member']['userID'] . "'";
		mysql_query($sql) or die (mysql_error());
		JavaScript::Alert("密碼已變更，請重新登入!");
		session_destroy();
		JavaScript::setURL("index.php?url=" . urlencode("member_login.php") ,"window.top");
	}
	include './include/db_close.php';
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
