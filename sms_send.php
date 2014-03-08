<?php

//require_once './include/config.php';

/*

$sms_phone = "0916184808";
$sms_content = "中 文 測 試";
$sms_memo = "簡訊測試";
*/
//$sms_phone = "";
if($sms_phone != "" && $sms_content != ""){
	$sms_content1 = urlencode(iconv("UTF-8", "big5", $sms_content));
	//$response = file_get_contents("http://202.39.48.216/kotsmsapi-1.php?username=JosephSu&password=19740318&dstaddr=$sms_phone&smbody=$sms_content1&response={$WEB_URL}sms_response.php");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, "202.39.48.216/kotsmsapi-1.php?username=53476975&password=hal1122&dstaddr=$sms_phone&smbody=$sms_content1&response=http://{$WEB_HOST}/sms_response.php");
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_USERAGENT, "Google Bot");
	$response = curl_exec($ch);
	curl_close($ch);


	$response = explode("=", $response);
	$sms_message = $response[1];
	$status = '';
	if($sms_message < 0){$status = 'ERROR';}
//	include './include/db_open.php';
	$sql = "INSERT INTO logSMS(dateSent, Phone, Content, Message, Memo, Status) VALUES (CURRENT_TIMESTAMP, '$sms_phone', '$sms_content', '$sms_message', '$sms_memo', '$status')";
	mysql_query($sql) or die (mysql_error());


	$result1 = mysql_query("SELECT YN FROM Config WHERE ID='sms'") or die(mysql_error());
	if($rs=mysql_fetch_array($result1)){
		$sms = $rs['YN'];
	}
	mysql_query("UPDATE Config SET YN = '" . (($sms > 0) ? ($sms-1) : 0) . "' WHERE ID = 'sms'") or die(mysql_error());


//	include './include/db_close.php';
}
?>