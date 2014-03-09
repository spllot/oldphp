<?php
include '../include/auth_admin.php';
require_once '../class/admin.php';
require_once '../class/pagging.php';
require_once '../class/system.php';
require_once '../class/tools.php';
if (!Permission::hasPermission($_SESSION['admin'], $_SESSION['permit'], $_MODULE->modules[$_MODULE->status][1])){exit("權限不足!!");}
include("../include/db_open.php");
$page = new Admin();
$page->setHeading($_MODULE->nameOf($_MODULE->status));
$page->addJSFile("../js/jquery.min.js");



$login['Total'] = 0;
$login['Yestoday'] = 0;
$login['Today'] = 0;
$login['Online'] = 0;

$sql = "SELECT DISTINCT LEFT(dateLogin, 10), userID FROM logLogin WHERE Status=1";
$result = mysql_query($sql) or die(mysql_error());
$today = date('Y-m-d');
$yestoday = date('Y-m-d', strtotime($today . "- 1 day"));

while($rs=mysql_fetch_array($result)){
	$login['Total']++;
	if($rs[0] == $yestoday)
		$login['Yestoday']++;
	if($rs[0] == $today)
		$login['Today']++;
}
/*
$sql = "SELECT userID, CURRENT_TIMESTAMP, lastMove, TIME_TO_SEC(TimeDiff(CURRENT_TIMESTAMP, lastMove)) FROM Member";
$result = mysql_query($sql) or die(mysql_error());
while($rs=mysql_fetch_array($result)){
	print_r($rs);
}
*/
$sql = "SELECT COUNT(*) FROM Member WHERE TIME_TO_SEC(TimeDiff(CURRENT_TIMESTAMP, lastMove))<=60";
$result = mysql_query($sql) or die(mysql_error());
if($rs=mysql_fetch_array($result)){
	$login['Online'] = $rs[0];
}
$level[1] = 0;
$level[2] = 0;
$level[3] = 0;
$level[4] = 0;
$level[5] = 0;
$level[6] = 0;
$level[7] = 0;
$level[8] = 0;
$member=0;
$seller=0;
$result = mysql_query("SELECT * FROM Member WHERE Level > 0");
while($rs=mysql_fetch_array($result)){
	$member++;
	$level[$rs['Level']] ++;
	if($rs['Seller'] == 2){
		$seller ++;
	}

}

$catalog = "";
$result = mysql_query("SELECT * FROM Catalog WHERE useFor='TYPE_PRO' AND Parent=0 ORDEr BY Sort") or die(mysql_error());
while($rs=mysql_fetch_array($result)){
	$catalog .= "<option value='" . $rs['No'] . "'>" . $rs['Name'] . "</option>";
}

$result = mysql_query("SELECT * FROM Product WHERE dateClose >= CURRENT_TIMESTAMP") or die(mysql_error());
$num[1][0] = 0;
$num[2][0] = 0;
$num[1][1] = 0;
$num[2][1] = 0;
while($rs=mysql_fetch_array($result)){
	$num[$rs['Mode']][$rs['Deliver']] ++;

}



$info =<<<EOD
<table width="100%">
	<tr>
		<td style="font-weight:bold">網站流量：</td>
	</tr>
	<tr>
		<td style="padding-left:20px">
		總登入量：{$login['Total']}
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="https://www.google.com/analytics/web/?hl=zh-TW&pli=1#dashboard/default/a37407627w65772823p67591376/" target="_blank">前往Google Analytics</a><br>
		昨日登入量：{$login['Yestoday']}<br>
		今日登入量：{$login['Today']}<br>
		目前線上登入數：{$login['Online']}
		</td>
	</tr>
	<tr>
		<td style="font-weight:bold">會員數量：</td>
	</tr>
	<tr>
		<td style="padding-left:20px">
		申請會員總量：{$member}
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		申請為賣家會員總量：{$seller}<br>
		會員等級統計數：<br>
		<table style="background:gray;" cellpadding="1" cellspacing="1">
			<Tr>
				<Td style="background:#FFFFFF; text-align:center; width:80px">等級1</td>
				<Td style="background:#FFFFFF; text-align:center; width:80px">等級2</td>
				<Td style="background:#FFFFFF; text-align:center; width:80px">等級3</td>
				<Td style="background:#FFFFFF; text-align:center; width:80px">等級4</td>
				<Td style="background:#FFFFFF; text-align:center; width:80px">等級5</td>
				<Td style="background:#FFFFFF; text-align:center; width:80px">等級6</td>
				<Td style="background:#FFFFFF; text-align:center; width:80px">等級7</td>
				<Td style="background:#FFFFFF; text-align:center; width:80px">等級8</td>
			</tr>
			<Tr>
				<Td style="background:#FFFFFF; text-align:center">{$level[1]}</td>
				<Td style="background:#FFFFFF; text-align:center">{$level[2]}</td>
				<Td style="background:#FFFFFF; text-align:center">{$level[3]}</td>
				<Td style="background:#FFFFFF; text-align:center">{$level[4]}</td>
				<Td style="background:#FFFFFF; text-align:center">{$level[5]}</td>
				<Td style="background:#FFFFFF; text-align:center">{$level[6]}</td>
				<Td style="background:#FFFFFF; text-align:center">{$level[7]}</td>
				<Td style="background:#FFFFFF; text-align:center">{$level[8]}</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="font-weight:bold">上架商品數量：</td>
	</tr>
	<tr>
		<td style="padding-left:20px">
		到店團購總量：{$num[1][0]}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		宅配團購總量：{$num[1][1]}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		到店廉售總量：{$num[2][0]}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		宅配廉售總量：{$num[2][1]}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
		<table>
			<tr>
				<td>到店團購：</td>
				<td><select id="s10" onChange="CHG(1, 0);"><option value=''>所有分類</option>{$catalog}</select></td>
				<td style="padding-left:20px">數量：<span id="n10">{$num[1][0]}</span></td>
			</tr>
			<tr>
				<td>宅配團購：</td>
				<td><select id="s11" onChange="CHG(1, 1);"><option value=''>所有分類</option>{$catalog}</select></td>
				<td style="padding-left:20px">數量：<span id="n11">{$num[1][1]}</span></td>
			</tr>
			<tr>
				<td>到店廉售：</td>
				<td><select id="s20" onChange="CHG(2, 0);"><option value=''>所有分類</option>{$catalog}</select></td>
				<td style="padding-left:20px">數量：<span id="n20">{$num[2][0]}</span></td>
			</tr>
			<tr>
				<td>宅配廉售：</td>
				<td><select id="s21" onChange="CHG(2, 1);"><option value=''>所有分類</option>{$catalog}</select></td>
				<td style="padding-left:20px">數量：<span id="n21">{$num[2][1]}</span></td>
			</tr>
		</table>
		</td>
	</tr>
</table>


EOD;
















$page->addContent($info);
$page->show();
include '../include/db_close.php';
?>
<script language="javascript">
	function CHG(mode, deliver){
		$.post(
			'status_product.php',
			{
				mode: mode,
				deliver: deliver,
				catalog: $("#s" + mode + deliver).val()
			},
			function(data)
			{
				$("#n" + mode + deliver).html(data);
			}
		);
	}
</script>