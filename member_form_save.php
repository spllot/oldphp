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
$branch = $_REQUEST['branch'];
$account = $_REQUEST['account'];
JavaScript::setCharset("UTF-8");
if($rname != "" && $rzip != "" && $raddress != "" && $bank != "" && $account != ""){
	include './include/db_open.php';
	$sql = "UPDATE Member SET rName='$rname', rZip='$rzip', rAddress='$raddress', uniNo='$unino', Bank='$bank', Branch='$branch', Account='$account', dateRequest=CURRENT_TIMESTAMP, dateUpdate=CURRENT_TIMESTAMP, updateBy = '" . $_SESSION['member']['Name'] . "', rLatitude='" . $latitude[0] . "', rLongitude='" . $latitude[1] . "', Seller = 2, dateApprove=CURRENT_TIMESTAMP WHERE userID = '" . $_SESSION['member']['userID'] . "'";
	mysql_query($sql) or die (mysql_error());

	$result = mysql_query("SELECT *, (SELECT COUNT(*) FROM Admin WHERE EMail=Member.userID) AS isAdmin FROM Member WHERE userID = '" . $_SESSION['member']['userID'] . "'") or die (mysql_error());
	$_SESSION['member']  = mysql_fetch_array($result);

		$m_subject = "InTimeGo即購網申請成為商品賣家通知信";
		$m_recipient = $_SESSION['member']['userID'];

		$m_content = <<<EOD
			<table>
				<tr>
					<td>
						親愛的 {$_SESSION['member']['Name']} ：
					</td>
				</tr>
				<tr>
					<td>
						此封信件由 InTimeGo即購網(本站) 所發出的。<br>
						恭禧您通過本站審核，成為商品賣家<br>
						您可登入本站進行商品上架。<br>
						<br>
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
		$m_memo = "申請成為商品賣家通知信";
		$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
		mysql_query($sql) or die (mysql_error());

	JavaScript::Alert("恭禧您通過本站審核，成為金流商品賣家，您可立即進行金流商品上架!");
	JavaScript::setURL("member.php" ,"window.parent.parent");
	include './include/db_close.php';
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
