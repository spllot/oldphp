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


	$sql = "SELECT * FROM Template WHERE ID='EX2'";
	$result = mysql_query($sql);
	if($rs1=mysql_fetch_array($result)){
		$t_email = str_replace("\n", "<br>", $rs1['Content']);
		$t_msg = str_replace("\n", "<br>", $rs1['Message']);
		$t_sms = str_replace("\n", "<br>", $rs1['SMS']);
		$t_subject = $rs1['Subject'];
	}



	$sql = "SELECT *, (SELECT Name FROM Member WHERE userID=logExport.Member) AS Name, (SELECT Phone FROM Member WHERE userID=logExport.Member) AS Phone , (SELECT SUM(Amount) FROM logTransaction WHERE Memo=logExport.ID AND useFor IN (11, 12)) AS Pay, (SELECT SUM(Amount) FROM logTransaction WHERE Owner=logExport.Member) AS Balance FROM logExport WHERE No IN ($memberlist) AND Status = 0";
	$result = mysql_query($sql) or die(mysql_error());
	if(mysql_num_rows($result) > 0){
		while($rs = mysql_fetch_array($result)){
			if($rs['dateProcess'] == '0000-00-00 00:00:00'){
				$fee = $rs['Fee'];
				$total = $rs['Amount'] - $fee;
				$sql = "UPDATE logExport SET dateProcess = CURRENT_TIMESTAMP, processBy='" . $_SESSION['admin'] . "', Status = 1 WHERE No = '" . $rs['No'] . "'";
				mysql_query($sql) or die(mysql_error());

				$m_subject = $t_subject;
				$m_recipient = $rs['Member'];

				$info = "您的匯出申請單號：{$rs['ID']}，申請金額：{$rs['Amount']}，手續費：-{$fee}，已匯出金額：{$total}";

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
								親愛的 {$rs['Name']} ：
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
		}
	}
	else{
		JavaScript::Alert("查無資料!!");	
	}
	include("../include/db_close.php");
	JavaScript::setURL("log_export.php?pageno=$pageno&keyword=$keyword", "window.parent");
}//if
else{
    JavaScript::Alert("輸入欄位不足!!");
}//else


?>