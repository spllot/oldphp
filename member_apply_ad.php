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

$fee = $_REQUEST["fee"];
$name = $_REQUEST["name"];
$url = $_REQUEST["url"];
$discount = $_REQUEST["discount"];
$usefor = $_REQUEST["usefor"];
$catalog = $_REQUEST["catalog"];
$days = $_REQUEST["days"];
$t = $_REQUEST["total"];

$icon = basename($_REQUEST["ad_picpath"]);
$src = 2;



JavaScript::setCharset("UTF-8");
if($name != "" && $url != "" && $icon != "" && $fee != "" && $days != "" && $t != ""){
	include './include/db_open.php';

	$result = mysql_query("SELECT * FROM Config") or die(mysql_error());
	while($rs=mysql_fetch_array($result)){
		$_CONFIG[$rs['ID']] = $rs['YN'];
	}
	$result = mysql_query("SELECT COALESCE(SUM(Amount), 0) as Amount FROM logTransaction WHERE Owner=(SELECT userID FROM Member WHERE No='" . $_SESSION['member']['No'] . "')");
	if($rs=mysql_fetch_array($result)){
		$balance = $rs['Amount'];
	}
	$total = 0;

	$total = $days * $_CONFIG['adfee' . $fee];

	if($balance >= $total){
		$approve = date('Y-m-d H:i:s');
		$dateexpire = date('Y-m-d H:i:s', strtotime($approve . " +" . $days . " day"));
		if($total > 0 && $t == $total){
			switch($fee){
				case 1:
					$sql = "INSERT INTO AD (Src, Link, Url, Icon, Caption, useFor, Sort, Country, Member, Days, dateSubmit, Cost, dateExpire) VALUES ('$src', '$link', '$url', '$icon', '$name', 'BANNER', '$sortOrder', '$country', '" . $_SESSION['member']['No'] . "', '$days', '$approve','$total', '$dateexpire')";
					$memo = "右側廣告";
					$msg = "您於" . date('Y-m-d H:i:s') . "在本站申購[前台右側廣告]刊登{$days}日，已抵付儲值金&#36;{$total}，即購網在此感謝您的支持與使用。";
					break;

				case 2:
					$sql = "INSERT INTO AD2 (Src, Link, Catalog, Url, Icon, Caption, useFor, Sort, Country, Discount, Member, Days, dateSubmit, Cost, dateExpire) VALUES ('$src', '$link', '$catalog', '$url', '$icon', '$name', '$usefor', '$sortOrder', '$country', '$discount', '" . $_SESSION['member']['No'] . "', '$days', '$approve','$total', '$dateexpire')";
					$memo = "下方廣告";
					$msg = "您於" . date('Y-m-d H:i:s') . "在本站申購[前台下方分類廣告]刊登{$days}日，已抵付儲值金&#36;{$total}，即購網在此感謝您的支持與使用。";
					break;
			}
			if($sql != ""){
//				echo $sql . "<br>";
				mysql_query($sql) or die(mysql_error());

				$sql = "INSERT INTO logTransaction(Owner, `Date`, Amount, Memo, useFor) VALUES ('" . $_SESSION['member']['userID'] . "', CURRENT_TIMESTAMP, '-{$total}', '{$memo}{$days}天', '15')";
//				echo $sql;
				mysql_query($sql) or die(mysql_error());
				JavaScript::Alert("謝謝您，我們將立即為您播放廣告!!");
				JavaScript::setURL("member_apply.php" ,"window.parent");

				$m_subject = "InTimeGo即購網廣告申購通知信";
				$m_content = <<<EOD
				<table>
					<tr>
						<td>
							親愛的 {$_SESSION['member']['Name']} ：
						</td>
					</tr>
					<tr>
						<td><br>
							此封信件由 InTimeGo即購網(本站) 所發出的。<br><br>
							{$msg} <br><br>
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


			$m_recipient = $_SESSION['member']['userID'];
			$m_memo = $m_subject;
			$sql = "INSERT INTO queueEMail(Subject, Recipient, Name, Content, dateRequested) VALUES ('$m_subject', '$m_recipient', '$name', '$m_content', CURRENT_TIMESTAMP)";
			mysql_query($sql) or die (mysql_error());



			}
			else{
				JavaScript::Alert("網路錯誤，請重新整理頁面!!");
			}
		}
		else{
			JavaScript::Alert("金額錯誤，可能日價已變更，請重新整理頁面!!");
		}
	}
	else{
		JavaScript::Alert("儲值金餘額({$balance})不足!!");
	}
	include './include/db_close.php';
}
else{
	JavaScript::Alert("輸入欄位不足!!");
}
?>
