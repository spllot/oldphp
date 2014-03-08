<?php
include './include/session.php';
require_once './class/javascript.php';
require_once './class/tools.php';
$code = $_REQUEST["code"];
$email = $_REQUEST["email"];
include("./include/db_open.php");
JavaScript::setCharset("UTF-8");
if ($code == "" || $email == ""){
    JavaScript::Alert("輸入欄位不足!!");
    exit;
}//if
else{
	$sql = "SELECT * FROM Modifier WHERE Owner = '$email' AND Code = binary'$code' AND dateExecuted = '0000-00-00 00:00:00'";
	$result = mysql_query($sql) or die (mysql_error());
	if($rs = mysql_fetch_array($result)){
		
		$result = mysql_query("SELECT * FROM Member WHERE userID='$email'");
		$member = mysql_fetch_array($result);

		mysql_query("UPDATE Modifier SET dateExecuted = CURRENT_TIMESTAMP, Code='' WHERE  Owner = '$email' AND Code = binary'$code' AND dateExecuted = '0000-00-00 00:00:00'");

		list($bank, $branch, $account, $amount, $fee) = explode("::", urldecode($rs['SQL']));

		$balance=0;
		$trust = 0;
		$result = mysql_query("SELECT COALESCE(SUM(Amount), 0) as Amount FROM logTransaction WHERE Owner='" . $email . "'");
		if($rs=mysql_fetch_array($result)){
			$balance = $rs['Amount'];
		}

		if($balance < $amount){
			JavaScript::Alert("餘額不足!目前餘額為{$balance}");
			exit;
		}





		$prefix = "E" . date('ymd');
		$result = mysql_query("SELECT ID FROM logExport WHERE ID LIKE '%$prefix%' ORDER BY ID DESC");
		if(mysql_num_rows($result) >0){
			list($curr_id)=mysql_fetch_row($result);
			$curr_no = (int)substr($curr_id, -4);
			
			$id = $prefix . substr("000" . ($curr_no + 1), -4);
		}
		else{
			$id = $prefix . "0001";
		}
		

		$sql = "SELECT * FROM Template WHERE ID='EX1'";
		$result = mysql_query($sql);
		if($rs=mysql_fetch_array($result)){
			$t_email = str_replace("\n", "<br>", $rs['Content']);
			$t_msg = str_replace("\n", "<br>", $rs['Message']);
			$t_sms = str_replace("\n", "<br>", $rs['SMS']);
			$t_subject = $rs['Subject'];
		}

		$sql = "insert into logExport(ID, dateRequest, Member, Bank, Branch, Account, Amount, Fee) VALUES('$id', CURRENT_TIMESTAMP, '" . $email . "', '" . $bank . "', '" . $branch . "', '" . $account . "', '$amount', '$fee')";
		mysql_query($sql) or die (mysql_error());

		$total = $amount - $fee;
		$sql = "INSERT INTO logTransaction(Owner, `Date`, Amount, Memo, useFor) VALUES ('" . $email . "', CURRENT_TIMESTAMP, '-$total', '" . $id . "', '11')";
		mysql_query($sql) or die(mysql_error());
		if($fee > 0){
			$sql = "INSERT INTO logTransaction(Owner, `Date`, Amount, Memo, useFor) VALUES ('" . $email . "', CURRENT_TIMESTAMP, '-$fee', '" . $id . "', '12')";	
			mysql_query($sql) or die(mysql_error());			
		}
		$info = "您的匯出申請單號：{$id}，申請金額：{$amount}，手續費：-{$fee}，應匯出總額：{$total}";

		$m_subject = $t_subject;
		$m_recipient = $_SESSION['member']['userID'];
		$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 {$member['Name']} ：
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
		$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '{$_SESSION['member']['Name']}', '$m_content', CURRENT_TIMESTAMP)";
		$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 {$member['Name']} ：
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
		JavaScript::Alert("申請已完成，儲值金已事先扣除，匯款將統一於每月的第一個星期一處理!");	
	
	}
	else{
	    JavaScript::Alert("資料錯誤，無法進行修改!!");
	}
}//else
JavaScript::setURL("./", "window");
include("./include/db_close.php");
?>