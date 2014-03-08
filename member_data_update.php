<?php
include './include/session.php';
require_once './class/javascript.php';
require_once './class/tools.php';
$code = $_REQUEST["code"];
$email = $_REQUEST["email"];
include("./include/db_open.php");
JavaScript::setCharset("UTF-8");
if ($code == "" || $email == ""){
    JavaScript::Alert("輸入欄位不足!!");
    exit;
}//if
else{
	$sql = "SELECT * FROM Modifier WHERE Owner = '$email' AND Code = binary'$code' AND dateExecuted = '0000-00-00 00:00:00'";
	$result = mysql_query($sql) or die (mysql_error());
	if($rs = mysql_fetch_array($result)){
		mysql_query("UPDATE Modifier SET dateExecuted = CURRENT_TIMESTAMP, Code='' WHERE  Owner = '$email' AND Code = binary'$code' AND dateExecuted = '0000-00-00 00:00:00'");
		mysql_query(urldecode($rs['SQL'])) or die(mysql_error());
		$result = mysql_query("SELECT * FROM Member WHERE userID='$email'");
		
		$rs = mysql_fetch_array($result);
		$_SESSION['member'] = $rs;
		$_SESSION['Latitude'] = $rs['Latitude'];
		$_SESSION['Longitude'] = $rs['Longitude'];
		$_SESSION['Address'] = $rs['Address'];
	    JavaScript::Alert("資料已變更!!");
	}
	else{
	    JavaScript::Alert("資料錯誤，無法進行修改!!");
	}
}//else
JavaScript::setURL("./", "window");
include("./include/db_close.php");
?>