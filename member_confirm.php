<?php
require_once './class/javascript.php';
require_once './class/tools.php';
$code = $_REQUEST["code"];
$email = $_REQUEST["email"];
include("./include/db_open.php");
JavaScript::setCharset("UTF-8");
$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}
if ($code == "" || $email == ""){
    JavaScript::Alert("輸入欄位不足!!");
    exit;
}//if
else{
	$sql = "SELECT * FROM Member WHERE userID = '$email' AND codeEMail = binary'$code' AND Status = '0'";
	$result = mysql_query($sql) or die (mysql_error());
	if(mysql_num_rows($result) == 1){
		$sql = "UPDATE Member SET Status = '1', Level = '1', dateConfirm = CURRENT_TIMESTAMP, dateUpdate = CURRENT_TIMESTAMP, updateBy = '確認信', codeEMail = ''  WHERE userID = '$email' AND codeEMail = binary'$code'";
		$rs = mysql_fetch_array($result);
		mysql_query($sql) or die (mysql_error());
		$m_subject = "InTimeGo即購網E-mail帳號啟用通知信";
		$m_recipient = $rs['userID'];
		$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 {$rs['Name']} ：
					</td>
				</tr>
				<tr>
					<td>
						這封信是由 InTimeGo即購網(本站) 所發送的。<br>
						恭喜您已經完成了本站的會員註冊程序！<br>
						您的E-mail帳號已啟用，<br>
						您可立即登入本站，使用本站會員功能。
						<br><br>

網站使用導引:<br><br>

關於會員要如何建置商品頁面, 可以參考以下連結說明:<br>
<a href="{$_CONFIG['urlF']}" target="_blank">{$_CONFIG['urlF']}</a><br><br>
關於會員後台如何設定商品行動販售功能, 可以參考以下之連結說明:<br>
<a href="{$_CONFIG['urlG']}" target="_blank">{$_CONFIG['urlG']}</a><br><br>
如欲申請成為金流商品賣家並了解後台功能說明，可以參考此連結:<br>
<a href="{$_CONFIG['urlH']}" target="_blank">{$_CONFIG['urlH']}</a><br><br>
使用本站如果問題之處, 非常歡迎您的詢問, 即購網感謝您的支持!<br><br>

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
		$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '" . $rs['Name'] . "', '$m_content', CURRENT_TIMESTAMP)";
		mysql_query($sql) or die (mysql_error());
		JavaScript::Alert("E-mail帳號啟用成功!!");
		JavaScript::Redirect("member_login.php");

	}
	else{
	    JavaScript::Alert("資料錯誤，無法進行啟用!!");
	}
}//else
JavaScript::setURL("./", "window");
include("./include/db_close.php");
?>