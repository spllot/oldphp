<?php
include './include/session.php';
require_once getcwd() . '/class/facebook.php';
require_once './class/tools.php';
require_once './class/javascript.php';


JavaScript::setCharset("UTF-8");
?>

<?
function fetchUrl($url){
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_TIMEOUT, 20);
     $retData = curl_exec($ch);
     curl_close($ch); 
 
     return $retData;
}

$product = $_REQUEST['product'];
$sn = $_REQUEST['member'];
$no = $product;
$e = $_REQUEST['e'];
$email = $_REQUEST['email'];


include './include/db_open.php';
$result = mysql_query("SELECT * FROM Member WHERE No='$sn'") or die(mysql_error());
$member = mysql_fetch_array($result);
$result = mysql_query("SELECT * FROM Product WHERE No='$no'") or die(mysql_error());
$data = mysql_fetch_array($result);
$url = "http://{$WEB_HOST}/";//product4_detail.php?no={$no}"

if($data['Mode'] == 2){
	if($data['Deliver'] == 0){
		$url .= "product4_detail.php?no={$no}";
	}
	else{
		$url .= "product5_detail.php?no={$no}";
	}
}
else if($data['Mode'] == 1){
	if($data['Deliver'] == 0){
		$url .= "product1_detail.php?no={$no}";
	}
	else{
		$url .= "product2_detail.php?no={$no}";
	}
}


$join = false;

$result = mysql_query("SELECT * FROM logActivity WHERE Product='{$no}' AND fbID='{$fb_uid}'") or die(mysql_error());
if(mysql_num_rows($result) > 0){
	$join = true;
}


$step1=false;
$step2=false;
$step3=false;


if($me){
	$step1 = true;
	$fb_login = <<<EOD
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td>&nbsp;</td>
				<td style="color:white;font-size:13px;text-align:right"><img src="https://graph.facebook.com/{$fb_uid}/picture"></td>
				<td style="width:10px"></td>
				<td valign="bottom" style="color:black; font-size:10pt">{$me['name']}</td>
			</tr>
		</table>
EOD;
	
	try {
	  $facebook->setAccessToken($accessToken);
	  $likes = $facebook->api("/me/likes/{$data['activity_page']}");
	  if( !empty($likes['data']) )
		  $step2=true;
	} catch (FacebookApiException $e) {
	  error_log($e);
	}
	
	$result = mysql_query("SELECT * FROM logShare WHERE fbID='$fb_uid' AND Product='$no'");
	if(mysql_num_rows($result) > 0){
		  $step3=true;
	}
}


if($member && $e == 1){
	$email = $member['userID'];
}
if($e == 2){
	$email = $me['email'];
}


if(!Tools::checkEMail($email)){JavaScript::Alert("電子郵件格式錯誤!!".$email);exit;}


if($step1 && $step2 && $step3){
	if(!$join){
		$sql = "INSERT INTO logActivity SET Product='$product', fbID='{$fb_uid}', dateJoined=CURRENT_TIMESTAMP, EMail='$email', Member='{$member['No']}'";
		//echo $sql;
		mysql_query($sql) or die(mysql_error());
		JavaScript::Alert("成功參加活動，祝您中獎!");
	}
	else{
		JavaScript::Alert("系統已有你的參加記錄，一個 Facebook 帳號只能參加一次!");
	}
}
else{
	JavaScript::Alert("請閱讀說明內容並完成[加入粉絲團步驟]，再按下[參加活動]!");
}

JavaScript::Execute("parent.setURL();");
include './include/db_close.php';
?>
