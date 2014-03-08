<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';

$userid = $_REQUEST['email'];
$captcha = $_REQUEST['captcha'];
JavaScript::setCharset("UTF-8");
if($userid != "" && $captcha != ""){
	if($captcha != $_SESSION['security_code']){JavaScript::Alert("驗證碼錯誤，請重新輸入!!");JavaScript::Execute("window.location.href = 'member_forgot.php';");exit;}
	if(!Tools::checkEMail($userid)){JavaScript::Alert("電子郵件格式錯誤!!");exit;}
	include './include/db_open.php';
	$sql = "SELECT * FROM Member WHERE userID = '$userid'";
	$result = mysql_query($sql) or die (mysql_error());
	if($rs=mysql_fetch_array($result)){
		$m_subject = "InTimeGo即購網密碼通知信";
		$m_recipient = $userid;
		$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 {$rs['Name']} ：
					</td>
				</tr>
				<tr>
					<td>
						此封信件由 InTimeGo即購網(本站) 所發出的。<br>
						您所查詢的密碼是：{$rs['userPass']}。<br><br>

					</td>
				</tr>
				<tr>
					<td>
						InTimeGo即購網<br>
						http://{$WEB_HOST}<br>
					</td>
				</tr>
			</table>

EOD;
		$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
		mysql_query($sql) or die (mysql_error());
		JavaScript::Alert("密碼通知信已發送至" . $userid . "，請檢查您的電子信箱!!");
		JavaScript::setURL("index.php?url=" . urlencode('member_login.php')  ,"window.parent");
	}
	else{
		JavaScript::Alert("E-mail帳號錯誤!!");
		JavaScript::Execute("window.location.href = 'member_forgot.php';");
	}
	include './include/db_close.php';
}
else{
	JavaScript::Alert("輸入欄位不足!!");
	JavaScript::Execute("window.location.href = 'member_forgot.php';");
}
?>
