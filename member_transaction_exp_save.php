<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
JavaScript::setCharset("UTF-8");
if(empty($_SESSION['member'])){
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
include './include/db_open.php';
$exp_level = 99;
$result = mysql_query("SELECT * FROM Config WHERE ID='exp'");
if($rs = mysql_fetch_array($result)){
	$exp_level = $rs['YN'];
}

if($_SESSION['member']['Level'] < $exp_level){
	JavaScript::Alert("您的會員等級未達{$exp_level}級無法申請匯出!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}



$bank = $_REQUEST['bank'];
$branch = $_REQUEST['branch'];
$account = $_REQUEST['account'];
$amount = $_REQUEST['amount'];
$fee = (($bank=="永豐銀行") ? 0 : 15);
if($bank != "" && $branch != "" && $account != "" && $amount != ""){

	$balance=0;
	$trust = 0;
	$result = mysql_query("SELECT COALESCE(SUM(Amount), 0) as Amount FROM logTransaction WHERE Owner='" . $_SESSION['member']['userID'] . "'");
	if($rs=mysql_fetch_array($result)){
		$balance = $rs['Amount'];
	}

	if($balance < $amount){
		JavaScript::Alert("餘額不足!目前餘額為{$balance}");
		exit;
	}
	$sql = $bank . "::" . $branch . "::" . $account . "::" . $amount . "::" . $fee;

	if($sql != ""){
		$code = Tools::newPassword(10);
		$m_subject = "InTimeGo即購網儲值金匯出申請確認信";
		$m_recipient = $_SESSION['member']['userID'];
		$confirm_url = "http://{$WEB_HOST}/member_transaction_exp_update.php?email=$m_recipient&code=$code";
		$date = date('Y-m-d H:i:s');
		$items = "銀行名稱：{$bank}<br>";
		$items .= "分支行名稱：{$branch}<br>";
		$items .= "用戶帳號：{$account}<br>";
		$items .= "儲值金匯出金額：{$amount}<br>";

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
						本站於 {$date} 收到您儲值金匯出申請：<br><br>
						{$items}<br>
						基於網站安全考量，請點選下列網址，以便完成儲值金匯出申請。<br>
						若此資料並非您所申請，代表您的資料已被駭客入侵，所以請不要點下列網址，建議此時需儘速到網站變更您的密碼<br><br>
						<a href="$confirm_url">$confirm_url</a><br><br>
						
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
		$sql = sprintf("INSERT INTO Modifier SET Owner='" . $_SESSION['member']['userID'] . "', `SQL`='%s', Code='$code'", urlencode($sql));
		mysql_query($sql) or die (mysql_error());
		$m_memo = "儲值金匯出申請確認信";
		$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
		mysql_query($sql) or die (mysql_error());			
	}
	JavaScript::Alert("請檢查您的信箱並點選確認網址，以便完成儲值金匯出申請!");
	JavaScript::setURL("member_transaction_exp.php" ,"window.parent");
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
include './include/db_close.php';
?>
