<?php
require_once './class/javascript.php';
JavaScript::setCharset("UTF-8");
$txid=$_REQUEST['txid'];
$amount=$_REQUEST['amount'];
$pay_type=$_REQUEST['pay_type'];
$status=$_REQUEST['status'];
$tid=$_REQUEST['tid'];
$verify=$_REQUEST['verify'];
$cname=$_REQUEST['cname'];
$caddress=$_REQUEST['caddress'];
$ctel=$_REQUEST['ctel'];
$cemail=$_REQUEST['cemail'];

$log = "";
foreach($_REQUEST as $key => $value){
	$log .=  $key . "=" . $value . "\n";
}

$file=$txid . "_" . time() . ".log";
$myFile = "./logs/$file";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $log);
fclose($fh);


$mid = "3231";
$v1 = "030302f0a32277e1244b5dd15bd9ad5b";
$v2 = "a5b3b9c9650e8bda2d143794e183e49e";

$msg = $v1 . "|" . $mid . "|" . $txid . "|" . $amount . "|" . $v2;
if($verify = md5($msg)){
	if($status == "2"){
		include './include/db_open.php';
		$result = mysql_query("SELECT *, (Amount + Fee) AS Total, (SELECT Name FROM Member WHERE userID=Payment.Member) AS Name FROM Payment WHERE ID='$txid' and datePaid='0000-00-00 00:00:00'");
		if($payment = mysql_fetch_array($result)){
			$sql = "UPDATE Payment SET datePaid = CURRENT_TIMESTAMP, Complete=1 WHERE ID = '$txid'";
			mysql_query($sql) or die(mysql_error());
			//1)信用卡儲值2)WEBATM3)虛擬轉帳4)交易扣款5)交易付款6)部落格行銷得分7)商品傳銷得分 
			$usefor = $payment['payBy'];
			$sql = "INSERT INTO logTransaction(Owner, `Date`, Amount, Memo, useFor) VALUES ('".$payment['Member']."', CURRENT_TIMESTAMP, '".$payment['Amount']."', '".$payment['ID']."', '$usefor')";
			mysql_query($sql) or die(mysql_error());
			JavaScript::Alert("儲值成功!");

			$sql = "SELECT * FROM Template WHERE ID='PAY'";
			$result = mysql_query($sql);
			if($rs=mysql_fetch_array($result)){
				$t_email = str_replace("\n", "<br>", $rs['Content']);
				$t_msg = str_replace("\n", "<br>", $rs['Message']);
				$t_sms = str_replace("\n", "<br>", $rs['SMS']);
				$t_subject = $rs['Subject'];
			}


			$info = "您的交易單號：{$txid}，付款金額：{$payment['Total']}，手續費：-{$payment['Fee']}，儲值金額：{$payment['Amount']}";


			$m_subject = $t_subject;
			$m_recipient = $payment['Member'];

			$m_content = <<<EOD
				<table>
					<tr>
						<td>
							親愛的 {$payment['Name']} ：
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
							親愛的 {$payment['Name']} 
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
		include './include/db_close.php';
	}
}
else{
	echo "ERROR";
}
list($y, $m)=explode("-", date('Y-m'));
JavaScript::Redirect("member.php?url=" . urlencode("member_transaction.php?y=$y&m=$m"));
?>
