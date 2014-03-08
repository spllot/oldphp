<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

include 'member_transaction_tab.php';
include './include/db_open.php';
$d = date('Y-m-01 00:00:00', strtotime("-2 month"));
$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}




$t4=$t5=0;
$sql = "SELECT *, Concat(Month(`Date`), '/', Day(`Date`)) AS Date1 FROM logTransaction WHERE useFor = '7' AND Owner='" . $_SESSION['member']['userID'] . "' AND `Date` >= '$d' ORDER BY `Date`";
$result = mysql_query($sql) or die (mysql_error());
while($rs=mysql_fetch_array($result)){
	$a1 .= "+$" . $rs['Amount'] . " (" . $rs['Date1'] . ")<br>";

}

$result =  mysql_query("SELECT *, Concat(Month(`Date`), '/', Day(`Date`)) AS Date1 FROM logTransaction WHERE useFor = '6' AND Owner='" . $_SESSION['member']['userID'] . "' AND `Date` >= '$d' ORDER BY `Date`") or die (mysql_error());
while($rs=mysql_fetch_array($result)){
	$a5 .= "+$" . $rs['Amount'] . " (" . $rs['Date1'] . ")<br>";

}

$sql = "SELECT *, Concat(Month(`Date`), '/', Day(`Date`)) AS Date1 FROM logTransaction WHERE useFor IN (1, 2, 3, 8) AND Owner='" . $_SESSION['member']['userID'] . "' AND `Date` >= '$d' ORDER BY `Date`";
//echo $sql;
$result =  mysql_query($sql) or die (mysql_error());
while($rs=mysql_fetch_array($result)){
	$a2 .= "+$" . $rs['Amount'] . " (" . $rs['Date1'] . ")<br>";

}

$result =  mysql_query("SELECT *, Concat(Month(`Date`), '/', Day(`Date`)) AS Date1 FROM logTransaction WHERE useFor IN (11, 12) AND Owner='" . $_SESSION['member']['userID'] . "' AND `Date` >= '$d' ORDER BY `Date`") or die (mysql_error());
while($rs=mysql_fetch_array($result)){
	$a3 .= "-$" . abs($rs['Amount']) . " (" . $rs['Date1'] . ")<br>";

}

//echo "SELECT SUM(Amount) AS Amount, CAST(`Date` AS DATE) AS Day FROM logTransaction WHERE useFor IN (13) AND Owner='" . $_SESSION['member']['userID'] . "' AND `Date` >= '$d' GROUP By `Day` ORDER BY `Day`";

$result =  mysql_query("SELECT SUM(Amount) AS Amount, CAST(`Date` AS DATE) AS Day FROM logTransaction WHERE useFor IN (13, 15) AND Owner='" . $_SESSION['member']['userID'] . "' AND `Date` >= '$d' GROUP By `Day` ORDER BY `Day`") or die (mysql_error());
while($rs=mysql_fetch_array($result)){
	$a4 .= "-$" . abs($rs['Amount']) . " (" . date('n/j', strtotime($rs['Day'])) . ")<br>";

}



$result =  mysql_query("SELECT IFNULL(SUM(Amount), 0) FROM logTransaction WHERE	`Date` >='$d' AND Owner='" . $_SESSION['member']['userID'] . "'") or die (mysql_error());
list($t1) = mysql_fetch_row($result);
$result =  mysql_query("SELECT IFNULL(SUM(Amount), 0) FROM logTransaction WHERE	`Date` < '$d' AND Owner='" . $_SESSION['member']['userID'] . "'") or die (mysql_error());
list($t2) = mysql_fetch_row($result);
$t3 = $t1 + $t2;

$t4 = 0;
$result = mysql_query("SELECT Amount FROM logReceiptSMS WHERE Member='" . $_SESSION['member']['No'] . "' order By dateCreate DESC LIMIT 1") or die(mysql_error());
if($rs=mysql_fetch_array($result)){
	$t4 = $rs['Amount'];
}

$result = mysql_query("SELECT IFNULL(SUM(Cost), 0) FROM logCoupon WHERE Member='" . $_SESSION['member']['No'] . "' AND Receipt='' AND Phone<>''") or die(mysql_error());
list($tmp)=mysql_fetch_row($result);
$t5 = $tmp;

$result = mysql_query("SELECT IFNULL(SUM(Cost), 0) FROM AD WHERE Member='" . $_SESSION['member']['No'] . "' AND Receipt=''") or die(mysql_error());
list($tmp)=mysql_fetch_row($result);
$t5 += $tmp;
$result = mysql_query("SELECT IFNULL(SUM(Cost), 0) FROM AD2 WHERE Member='" . $_SESSION['member']['No'] . "' AND Receipt=''") or die(mysql_error());
list($tmp)=mysql_fetch_row($result);
$t5 += $tmp;


include './include/db_close.php';
$WEB_CONTENT = <<<EOD

<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td align="center" style="padding:10px; padding-top:20px">
			<table width="100%" cellpadding="1" cellspacing="1" bgcolor="#000000">
				<tr>
					<td width="20%" style="height:40px; text-align:center; background:white">行銷儲值金所得</td>
					<td width="20%" style="height:40px; text-align:center; background:white">傳播儲值金所得</td>
					<td width="20%" style="height:40px; text-align:center; background:white">儲值現金購買</td>
					<td width="20%" style="height:40px; text-align:center; background:white">儲值金匯出</td>
					<td width="20%" style="height:40px; text-align:center; background:white">儲值金抵用</td>
				</tr>
				<tr>
					<td style="height:40px; text-align:center; background:white; color:red; padding-top:10px; vertical-align:top">{$a5}&nbsp;</td>
					<td style="height:40px; text-align:center; background:white; color:red; padding-top:10px; vertical-align:top">{$a1}&nbsp;</td>
					<td style="height:40px; text-align:center; background:white; color:red; padding-top:10px; vertical-align:top">{$a2}&nbsp;</td>
					<td style="height:40px; text-align:center; background:white; color:red; padding-top:10px; vertical-align:top">{$a3}&nbsp;</td>
					<td style="height:40px; text-align:center; background:white; color:red; padding-top:10px; vertical-align:top">{$a4}&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="text-align:left; padding:10px">
			3個月以前儲值金剩餘 = $ {$t2}</font><br>
			3個月以內儲值金剩餘 = $ {$t1}</font><br>
			目前儲值金總計(3個月以前儲值金剩餘 + 3個月以內儲值金剩餘) = $ {$t3}</font><br><br>
			上次優惠憑證簡訊發送 + 廣告申購總計 = $ {$t4}</font>&nbsp;(已開立開票)<br>
			這次優惠憑證簡訊發送 + 廣告申購總計 = $ {$t5}</font>
		</td>
	</tr>
</table>


EOD;

include 'template2.php';
?>

