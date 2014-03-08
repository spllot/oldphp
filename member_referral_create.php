<?php
include './include/session.php';
require_once './class/javascript.php';
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

include './include/db_open.php';
$no = $_REQUEST['no'];

$sql = "SELECT * FROM Member WHERE Referral = '" . $_SESSION['member']['Phone'] . "' AND Phone <> '" . $_SESSION['member']['Phone'] . "' AND No IN (SELECT Member FROM Product WHERE dateApprove <> CURRENT_TIMESTAMP) ORDER BY Name";
$result = mysql_query($sql) or die(mysql_error());
echo <<<EOD
	<table style="width:100%">
		<tr>
			<td style="width:100px; line-height:22px; background:#b5b2b5;text-align:center">編號</td>
			<td style="width:100px; line-height:22px; background:#b5b2b5;text-align:center">姓名</td>
			<td style="width:100px; line-height:22px; background:#b5b2b5;text-align:center">手機號碼</td>
			<td style="; line-height:22px; background:#b5b2b5;text-align:center">e-mail</td>
		</tr>
EOD;


while($rs = mysql_fetch_array($result)){
	$serial = str_pad($rs['No'], 5, '0', STR_PAD_LEFT);
	echo <<<EOD
		<tr>
			<td align="center">{$serial}</td>
			<td align="center">{$rs['Name']}</td>
			<td align="center">{$rs['Phone']}</td>
			<td>{$rs['userID']}</td>
		</tr>
EOD;
}

echo "</table>";
include './include/db_close.php';
?>
