<?php
require_once './class/javascript.php';
require_once './class/tools.php';
$email = $_REQUEST["email"];
include("./include/db_open.php");
$feedback['err'] = 0;

if ($email == ""){
	$feedback['err'] = 1;
}//if
else{
	$result = mysql_query("SELECT * FROM Config");
	while($rs = mysql_fetch_array($result)){
		$_CONFIG[$rs['ID']] = $rs['YN'];
	}

	$sql = "SELECT * FROM Member WHERE userID = '$email' AND Status = '0'";
	$result = mysql_query($sql) or die (mysql_error());
	if($data = mysql_fetch_array($result)){
		$m_subject = "InTimeGo即購網會員註冊確認信";
		$m_recipient = $email;
		$confirm_url = "http://{$WEB_HOST}/member_confirm.php?email=$email&code=" . $data['codeEMail'];

		$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 {$data['Name']} ：
					</td>
				</tr>
				<tr>
					<td>
						此封信件由 InTimeGo即購網(本站) 所發出的。<br>
								您的電子郵件($email)已在本站註冊了一個新帳號，目前該帳號尚未啟動
						請點選以下連結來啟動您的帳號。<br><br>
						<a href="$confirm_url">$confirm_url</a><br><br>
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

	}
	else{
		$feedback['err'] = 2;
	}
}//else
include("./include/db_close.php");
echo json_encode($feedback);
?>