<?php
include '../include/auth_admin.php';
require_once '../class/system.php';
require_once("../class/javascript.php");
require_once("../class/tools.php");
JavaScript::setCharset("UTF-8");
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->log_payment][1])){exit("權限不足!!");}
$pageno = Tools::parseInt2($_POST["pageno"], 1);
$mno = $_POST["mno"];
$userid = $_POST["userid"];
$status = $_POST["status"];
$dateline = date('Y-m-d 00:00:00');
include("../include/db_open.php");

$sql = "SELECT * FROM Template WHERE ID='PAY' OR ID='FAL'";
$result = mysql_query($sql);
while($rs=mysql_fetch_array($result)){
	$Template[$rs['ID']] = $rs;
}

$sql = "SELECT Amount, Member.userID, Member.Name FROM logATM INNER JOIN Member ON Member.No=logATM.Member WHERE logATM.Status = 0 AND logATM.No='$mno'";
$result = mysql_query($sql) or die(mysql_error());
if($rs=mysql_fetch_array($result)){
	$sql = "UPDATE logATM SET Status = '$status', dateUpdate=CURRENT_TIMESTAMP WHERE Status = 0 AND No='$mno'";
	mysql_query($sql) or die("資料庫錯誤：" . mysql_error());
	$m_recipient = $rs['userID'];
	$name = $rs['Name'];
	$result1 = mysql_query("SELECT * FROM logATM WHERE No='$mno'") or die(mysql_error());
	$log = "";
	if($rs1=mysql_fetch_array($result1)){
		$log .= "儲值金匯入金額：{$rs1['Amount']}<Br>";
		$log .= "你的帳戶後五碼：{$rs1['Account']}<Br>";
		$log .= "你的銀行代碼：{$rs1['Bank']}<Br>";
		$log .= "匯款日期時間：{$rs1['dateTrans']}<Br>";
	}
	if($status == 1){
		$sql = "INSERT INTO logTransaction(Owner, `Date`, Amount, Memo, useFor) VALUES ('" . $rs['userID'] . "', CURRENT_TIMESTAMP, '" . $rs['Amount'] . "', 'ATM轉帳儲值', '8')";
		mysql_query($sql) or die("資料庫錯誤：" . mysql_error());
		$m_subject =  $Template['PAY']['Subject'];	
		$m_content = str_replace("\n", "<br>", $Template['PAY']['Content']);
		$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 {$name} ：
					</td>
				</tr>
				<tr>
					<td>
						此封信件由 InTimeGo即購網(本站) 所發出的。<br><br>
						{$m_content}<br><br>
						您的匯款資料：<br>{$log}
					</td>
				</tr>
				<tr>
					<td><br>
						InTimeGo即購網<br>
						http://{$WEB_HOST}<br>
					</td>
				</tr>
			</table>
EOD;

		$m_memo = $m_subject;
		$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
		mysql_query($sql) or die (mysql_error());			
	}
	else{
		$m_subject =  $Template['FAL']['Subject'];	
		$m_content = str_replace("\n", "<br>", $Template['FAL']['Content']);
		$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 {$name} ：
					</td>
				</tr>
				<tr>
					<td>
						此封信件由 InTimeGo即購網(本站) 所發出的。<br><br>
						{$m_content}<br><br>
						您的匯款資料：<br>{$log}
					</td>
				</tr>
				<tr>
					<td><br>
						InTimeGo即購網<br>
						http://{$WEB_HOST}<br>
					</td>
				</tr>
			</table>
EOD;
		echo $m_subject;
		$m_memo = $m_subject;
		$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
		mysql_query($sql) or die (mysql_error());			
	}




}
include("../include/db_close.php");
JavaScript::Redirect("log_payment.php?pageno=$pageno&userid=$userid");
?>