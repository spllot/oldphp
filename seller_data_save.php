<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}
$userid = $_REQUEST['email'];
$latitude = explode(",", str_replace(array("(", ")", " "), "", $_REQUEST['latitude']));
$rname = $_REQUEST['rname'];
$rzip = $_REQUEST['rzip'];
$raddress = $_REQUEST['raddress'];
$unino = $_REQUEST['unino'];
$bank = $_REQUEST['bank'];
$bno = $_REQUEST['bno'];
$branch = $_REQUEST['branch'];
$account = $_REQUEST['account'];
JavaScript::setCharset("UTF-8");
if($rname != "" && $rzip != "" && $raddress != "" && $bank != "" && $branch != "" && $account != ""){
	include './include/db_open.php';
	$sql = "UPDATE Member SET rName='$rname', rZip='$rzip', uniNo='$unino', bNo='$bno', Bank='$bank', Branch='$branch', dateUpdate=CURRENT_TIMESTAMP, updateBy = '" . $_SESSION['member']['Name'] . "', rLatitude='" . $latitude[0] . "', rLongitude='" . $latitude[1] . "' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
	mysql_query($sql) or die (mysql_error());

	$sql = "";
	$msg = "資料已修改!";
	if($raddress != $_SESSION['member']['rAddress'] && $account != $_SESSION['member']['Account']){
		$sql = "UPDATE Member SET rAddress='$raddress', Account='$account' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
		$msg = "變更發票地址及用戶帳號須經由email確認，請檢查您的信箱並點選確認網址，以便修改您的發票地址及用戶帳號\\n其它資料已更新!";
		$items = "發票地址原為：{$_SESSION['member']['rAddress']}，改為：{$raddress}<br>";
		$items .= "用戶帳號原為：{$_SESSION['member']['Account']}，改為：{$account}<br>";
	}
	else if($raddress != $_SESSION['member']['rAddress']){
		$sql = "UPDATE Member SET rAddress='$raddress' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
		$msg = "變更發票地址須經由email確認，請檢查您的信箱並點選確認網址，以便修改您的發票地址\\n其它資料已更新!";
		$items = "發票地址原為：{$_SESSION['member']['rAddress']}，改為：{$raddress}<br>";
	}
	else if($account != $_SESSION['member']['Account']){
		$sql = "UPDATE Member SET Account='$account' WHERE userID = '" . $_SESSION['member']['userID'] . "'";
		$msg = "變更用戶帳號須經由email確認，請檢查您的信箱並點選確認網址，以便修改您的用戶帳號\\n其它資料已更新!";
		$items = "用戶帳號原為：{$_SESSION['member']['Account']}，改為：{$account}<br>";
	}

	if($sql != ""){
		$code = Tools::newPassword(10);
		$m_subject = "InTimeGo即購網賣家資料修改確認信";
		$m_recipient = $_SESSION['member']['userID'];
		$confirm_url = "http://{$WEB_HOST}/member_data_update.php?email=$m_recipient&code=$code";

		$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 {$_SESSION['member']['Name']}：
					</td>
				</tr>
				<tr>
					<td>
						此封信件由 InTimeGo即購網(本站) 所發出的。<br>
								本站於 {$date} 收到您變更資料的要求：<br>
								{$items}
								基於網站安全考量，請點選下列網址，以便進行資料修改。<br>
								若此資料並非您所變更，代表您的資料已被駭客入侵，所以請不要點下列網址，建議此時需儘速到網站變更您的密碼<br><br>
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
		$m_memo = "賣家資料修改確認信";
		$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
		mysql_query($sql) or die (mysql_error());			
	}


	JavaScript::Alert($msg);
	JavaScript::setURL("seller_data.php" ,"window.parent");
	include './include/db_close.php';
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
