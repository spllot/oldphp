<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/javascript.php';
require_once '../class/tools.php';
require_once '../class/system.php';
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->log_export][1])){exit("權限不足!!");}
$memberlist = $HTTP_POST_VARS["memberlist"];
$userid = $HTTP_POST_VARS["userid"];
if ($memberlist <> ""){
    include("../include/db_open.php");
    $sql = "SELECT *, (SELECT Name FROM Member WHERE userID=logExport.Member) AS Name, (SELECT Phone FROM Member WHERE userID=logExport.Member) AS Phone FROM logExport WHERE No IN ($memberlist) AND dateProcess='0000-00-00 00:00:00' AND Status = 0";
    $result = mysql_query($sql) or die("資料庫錯誤：" . mysql_error());
	while($rs = mysql_fetch_array($result)){
		$sql = "UPDATE logExport SET dateCancel = CURRENT_TIMESTAMP, cancelBy='" . $_SESSION['admin'] . "', Status = 2 WHERE No = '" . $rs['No'] . "'";
		mysql_query($sql) or die(mysql_error());
		$sql = "DELETE FROM logTransaction WHERE Memo = '" . $rs['ID'] . "'";
		mysql_query($sql) or die(mysql_error());

		$sql = "SELECT * FROM Template WHERE ID='EX3'";
		$result = mysql_query($sql);
		if($rs1=mysql_fetch_array($result)){
			$t_email = str_replace("\n", "<br>", $rs1['Content']);
			$t_msg = str_replace("\n", "<br>", $rs1['Message']);
			$t_sms = str_replace("\n", "<br>", $rs1['SMS']);
			$t_subject = $rs1['Subject'];
		}
		$m_subject = $t_subject;
		$m_recipient = $rs['Member'];

		$info = "原申請單號：{$rs['ID']}，原申請金額：{$rs['Amount']}，已退回：{$rs['Amount']}";

		$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 {$rs['Name']} ：
					</td>
				</tr>
				<tr>
					<td>
						此封信件由 InTimeGo即購網(本站) 所發出的。<br><br>
						{$t_email}<br><br>
						{$info}<br><br>
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


		$m_memo = $t_subject;
		$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '{$rs['Name']}', '$m_content', CURRENT_TIMESTAMP)";
		$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 {$_SESSION['member']['Name']} ：
					</td>
				</tr>
				<tr>
					<td>
						此封信件由 InTimeGo即購網(本站) 所發出的。<br><br>
						{$t_msg}<br><br>
						{$info}<br><br>
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

		mysql_query($sql) or die (mysql_error());
		$sql = "INSERT INTO Message(Subject, Content, Sender, `To`, dateSent, Type) VALUES ('$m_subject', '$m_content', 'service@intimego.com', '$m_recipient', CURRENT_TIMESTAMP, '1');";
		mysql_query($sql) or die (mysql_error());

	}
	JavaScript::Alert("申請已取消，儲值金(含手續費)已退回會員帳戶!");
	JavaScript::setURL("log_export.php?pageno=$pageno&keyword=$keyword", "window.parent");
    include("../include/db_close.php");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else
?>