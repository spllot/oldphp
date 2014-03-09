<?php
include '../include/auth_admin.php';
require_once '../class/javascript.php';
require_once '../class/mail.php';
require_once '../class/tools.php';
require_once '../class/system.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->member][1])){exit("權限不足!!");}

$pageno = Tools::parseInt2($_POST["pageno"], 1);
$userid = $_POST["userid"];
$act = $_POST["act"];
$keyword = $_REQUEST["keyword"];
$status = $_REQUEST["status"];
$level = $_REQUEST["level"];
$seller = $_REQUEST["seller"];
if ($userid == ""){
    JavaScript::Alert("輸入欄位不足!!");
}//if
else{
	include("../include/db_open.php");
	$result = mysql_query("SELECT * FROM Config");
	while($rs = mysql_fetch_array($result)){
		$_CONFIG[$rs['ID']] = $rs['YN'];
	}
	$result = mysql_query("SELECT * FROM Member where userID = '" . $userid . "'");
	$member = mysql_fetch_array($result);
	switch($act){
		case 1:
			mysql_query("UPDATE Member SET Level = 1, Status=1, dateConfirm=CURRENT_TIMESTAMP WHERE userID='" . $userid . "'");
			$m_subject = "InTimeGo即購網E-mail帳號啟用通知信";
			$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 {$member['Name']} ：
					</td>
				</tr>
				<tr>
					<td>
						此封信件由 InTimeGo即購網(本站) 所發出的。<br>
						恭喜您，您的E-mail帳號已啟用，<br>
						您可立即登入本站，使用本站會員功能。<br><br>

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
			break;
		case 2:
			mysql_query("UPDATE Member SET Status=0, dateConfirm='0000-00-00 00:00:00' WHERE userID='" . $userid . "'");
			mysql_query("DELETE FROM Member WHERE userID='" . $userid . "'");
			$m_subject = "InTimeGo即購網E-mail帳號停用通知信";
			$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 {$member['Name']} ：
					</td>
				</tr>
				<tr>
					<td>
						此封信件由 InTimeGo即購網(本站) 所發出的。<br>
						很抱歉通知您，您的E-mail帳號已被取消<br>
						若要使用本站會員功能，請重新註冊<br>
						若有問題，請與本站客服人員聯絡。<br>
						謝謝您的配合<br><br>

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
			break;
		case 3:
			mysql_query("UPDATE Member SET dateApprove=CURRENT_TIMESTAMP, Seller=2 WHERE userID='" . $userid . "'");
			$m_subject = "InTimeGo即購網商品賣家審核通知信";
			$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 {$member['Name']} ：
					</td>
				</tr>
				<tr>
					<td>
						此封信件由 InTimeGo即購網(本站) 所發出的。<br>
						恭禧您通過本站審核，成為商品賣家<br>
						您可登入本站進行商品上架。<br>
						謝謝您的配合及耐心等待<br><br>

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
			break;
		case 4:
			mysql_query("UPDATE Member SET dateApprove='0000-00-00 00:00:00', Seller=3 WHERE userID='" . $userid . "'");
			$m_subject = "InTimeGo即購網取消賣家資格通知信";
			$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 {$member['Name']} ：
					</td>
				</tr>
				<tr>
					<td>
						此封信件由 InTimeGo即購網(本站) 所發出的。<br>
						很抱歉通知您，您的賣家資格已被取消<br>
						若有問題，請與本站客服人員聯絡。<br>
						謝謝您的配合<br><br>

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
			break;
	}
	$m_recipient = $userid;
	$m_memo = $m_subject;
	$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
	mysql_query($sql) or die (mysql_error());
	include("../include/db_close.php");
}//else
JavaScript::Redirect("member.php?status=$status&level=$level&seller=$seller&pageno=$pageno&keyword=$keyword&status=$status&level1=$level1");
?>