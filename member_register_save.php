<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';

$userid = $_REQUEST['email'];
$latitude = explode(",", str_replace(array("(", ")", " "), "", $_REQUEST['latitude']));
$userpass = $_REQUEST['pass1'];
$userpass1 = $_REQUEST['pass2'];
$name = $_REQUEST['name'];
$phone = $_REQUEST['phone'];
$referral = $_REQUEST['referral'];
$address = $_REQUEST['address'];
$captcha = $_REQUEST['captcha'];
$subscribe = $_REQUEST['subscribe'];
$subscribearea = $_REQUEST['subscribe_area'];
$agree = $_REQUEST['agree'];// => 1
JavaScript::setCharset("UTF-8");
if($agree == "1"){

if($userid != "" && $userpass != "" && $userpass1 != "" && $name != "" && $phone != "" && $address != "" && $captcha != ""){
	if($captcha != $_SESSION['security_code']){JavaScript::Alert("驗證碼錯誤，請重新輸入!!");JavaScript::Execute("parent.RefreshImage('imgCaptcha');");exit;}
	if(!Tools::checkPassword($userpass)){JavaScript::Alert("密碼長度必須為6-12個字!!");exit;}
	if(!Tools::checkEMail($userid)){JavaScript::Alert("電子郵件格式錯誤!!");exit;}
	if($userpass != $userpass1){JavaScript::Alert("密碼不符!!");exit;}
	include './include/db_open.php';
	
	if($referral != "" && strlen($referral) != 10){
		$result = mysql_query("SELECT * FROM Project WHERE Code='$referral'") or die (mysql_error());
		if(mysql_num_rows($result) == 0){
			JavaScript::Alert("抱歉! 無此專案代碼，或者您可填入介紹人手機號碼。");
			exit;
		}
	}



	$sql = "SELECT * FROM Member WHERE userID = '$userid' OR Phone = '$phone'";
	$result = mysql_query($sql) or die (mysql_error());
	if(mysql_num_rows($result) > 0){
		JavaScript::Alert("E-Mail或手機已被使用!!");
	}
	else{

		$result = mysql_query("SELECT * FROM Config");
		while($rs = mysql_fetch_array($result)){
			$_CONFIG[$rs['ID']] = $rs['YN'];
		}


		$code = Tools::newPassword(10);
		$sql = "INSERT INTO Member(userID, userPass, Name, Nick, Phone, Referral, Subscribe, subscribeArea, Address, Address0, dateRegister, dateUpdate, codeEMail, updateBy, Latitude, Longitude, Latitude0, Longitude0) VALUES('$userid', '$userpass', '$name', '$name', '$phone', '$referral', '$subscribe', '$subscribearea', '$address', '$address', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$code', '註冊', '" . $latitude[0] . "', '" . $latitude[1] . "', '" . $latitude[0] . "', '" . $latitude[1] . "')";
		mysql_query($sql) or die (mysql_error());
		$m_subject = "InTimeGo即購網會員註冊確認信";
		$m_recipient = $userid;
		$confirm_url = "http://{$WEB_HOST}/member_confirm.php?email=$userid&code=$code";

		$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 $name ：
					</td>
				</tr>
				<tr>
					<td>
						此封信件由 InTimeGo即購網(本站) 所發出的。<br>
								您的電子郵件($userid)已在本站註冊了一個新帳號，目前該帳號尚未啟動
						請點選以下連結來啟動您的帳號。<br><br>
						<a href="$confirm_url">$confirm_url</a><br><br>

網站使用導引:<br><br>

您可以點選右邊的連結說明, 以了解<a href="{$_CONFIG['urlF']}" target="_blank">如何提案建置商品&服務頁面</a>。<br><br>
您可以點選右邊的連結說明, 以了解<a href="{$_CONFIG['urlG']}" target="_blank">如何設定行動販售與服務功能</a>。<br><br>
您可以點選右邊的連結說明, 以了解<a href="{$_CONFIG['urlH']}" target="_blank">如何申請成為金流商品賣家</a>。<br><br>
使用本站如果問題之處, 非常歡迎您的詢問, 即購網感謝您的支持!<br><br>


					</td>
				</tr>
				<tr>
					<td>
						如果您從未造訪本站，請不必理會此封信件<br><br>
						InTimeGo即購網<br>
						http://{$WEB_HOST}<br>
					</td>
				</tr>
			</table>

EOD;
		$m_memo = "會員註冊確認信";
		$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
		mysql_query($sql) or die (mysql_error());
		JavaScript::Alert("確認信已發送至" . $userid . "，請檢查您的電子信箱!!");
		JavaScript::setURL("member_guild.php?email={$m_recipient}" ,"window.parent");
	}
	include './include/db_close.php';
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}

	}
	else{
		JavaScript::Alert("請仔細閱讀電子商務服務條款，並勾選願意遵守!");
	}


?>
