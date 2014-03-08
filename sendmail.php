<?php
require_once '/home/intimego/www/class/class.phpmailer.php';
include '/home/intimego/www/include/db_open.php';
if(mysql_query("INSERT INTO Process SET Program='sendmail.php', dateStart=CURRENT_TIMESTAMP")){






$sql = "SELECT * FROM queueEMail WHERE dateSent = '0000-00-00 00:00:00' AND Retry < 3 AND dateRequested <= CURRENT_TIMESTAMP ORDER BY dateRequested ASC LIMIT 60";
echo $sql . "\n";
$result = mysql_query($sql) or die (mysql_error());
$m_counts = 0;
if(mysql_num_rows($result)>0){

	mb_internal_encoding('UTF-8');
	$mail = new PHPMailer();
	$mail->IsSMTP();  
	$mail->SMTPAuth = true;  
//	$mail->SMTPSecure = "ssl";
	$mail->Host = "intimego.com";
	$mail->Port = 25;
	$mail->Username = "service@intimego.com";
	$mail->Password = "abc1122";
      
	$mail->From       = "service@intimego.com";
	$mail->FromName   = mb_encode_mimeheader("InTimeGo即購網", "UTF-8");

	$m_list = "";
	while($rs = mysql_fetch_array($result)){
		$m_subject = $rs["Subject"];
		$m_email = $rs["Recipient"];
		$m_content = <<<EOD
	<html>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>{$m_subject}</title>
		<body>
		{$rs["Content"]}
		</body>
	</html>
EOD;
		$m_name = $rs["Name"];
		if($m_subject != "" && $m_email != "" && $m_content != ""){
			$mail->Subject    = mb_encode_mimeheader($m_subject, "UTF-8");
			$mail->AltBody    = "請使用支援HTML功能之Email信箱, 來閱讀本信件!";
			$mail->MsgHTML($m_content);
			$mail->ClearAddresses();
			$mail->AddAddress($m_email, mb_encode_mimeheader($m_name, "UTF-8"));
			if($rs['CC']){
				$mail->AddCC($rs['CC']);
			}
			if($mail->Send()) {
				$m_list .= $rs["No"] . ",";
				$sql = "UPDATE queueEMail SET dateSent = CURRENT_TIMESTAMP WHERE No = '" . $rs["No"] . "'";
				mysql_query($sql) or die (mysql_error());
				echo $sql . "\n";
				$m_counts ++;			
			}
			else{
				$sql = "UPDATE queueEMail SET Retry = Retry+1,  Memo = '" . $mail->ErrorInfo . "' WHERE No = '" . $rs["No"] . "'";
				echo $sql . "\n";
				mysql_query($sql) or die (mysql_error());
			}
		}
	}
	if($m_list != ""){
		$m_list = substr($m_list, 0, strlen($m_list) - 1);
		$sql = "INSERT INTO logEMail (Subject, Content, Recipient, Memo, dateRequested, dateSent, Status) SELECT Subject, Content, Recipient, Memo, dateRequested, dateSent, 1 FROM queueEMail WHERE No IN ($m_list)";
		echo $sql . "\n";
		mysql_query($sql) or die (mysql_error());
		$sql = "DELETE FROM queueEMail WHERE No IN ($m_list)";
		echo $sql . "\n";
		mysql_query($sql) or die (mysql_error());
	}
	$str_result = "共寄出" . $m_counts . "封信件";
	$script = basename($_SERVER['SCRIPT_NAME']);
	mysql_query("INSERT INTO logScripts(Script, Date, Result) VALUES ('$script', CURRENT_TIMESTAMP, '$str_result')") or die (mysql_error());
}




	mysql_query("DELETE FROM Process WHERE  Program='sendmail.php'");
}
include '/home/intimego/www/include/db_close.php';
?>