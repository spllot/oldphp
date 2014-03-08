<?php
include './include/session.php';
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Member WHERE userID = '" . $_SESSION['member']['userID'] . "'");
$member = mysql_fetch_array($result);
$balance=0;
$trust = 0;
$result = mysql_query("SELECT COALESCE(SUM(Amount), 0) as Amount FROM logTransaction WHERE Owner='" . $_SESSION['member']['userID'] . "'");
if($rs=mysql_fetch_array($result)){
	$balance = $rs['Amount'];
}
$result = mysql_query("SELECT COALESCE(SUM(Quality), 0) as Amount FROM logRating WHERE Owner='" . $_SESSION['member']['No'] . "'");
if($rs=mysql_fetch_array($result)){
	$trust = $rs['Amount'];
}

$trust = (($_SESSION['member']['Seller']==2) ? "，賣家評價：" . number_format($trust) . "" : "");
include './include/db_close.php';

//print_r($_SESSION['member']);
?>

<div>
	<table cellpadding="0" cellspacing="0" border="0" align="right">
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0" border="0" align="right">
					<tr>
						<td height=16 valign=middle><img align=middle height=16 src="./images/logout.gif" border=0 onMouseOver="this.src='./images/logout.gif';" onMouseOut="this.src='./images/logout.gif';"></td>
						<td height=16 valign=middle>&nbsp;&nbsp;<a href="member_logout2.php" class="admin_top_link">登出</a></td>
						<td height=16 valign=middle width=30>&nbsp;</td>
						<td height=16 valign=middle><img align=middle height=16 src="./images/back.gif" border=0 onMouseOver="this.src='./images/back.gif';" onMouseOut="this.src='./images/back.gif';"></td>
						<td height=16 valign=middle>&nbsp;&nbsp;<a href="./" class="admin_top_link">回到首頁</a></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height=30></td></tr>
		<tr>
			<td style="padding-top:5px">
				<table cellpadding="0" cellspacing="0" border="0" align="right">
					<tr>
						<td style="font-family:Arial Unicode MS, 微軟正黑體;color:#003300;font-size:14px;">Hi, <?=$_SESSION['member']['Nick']?>(等級：<?=$_SESSION['member']['Level']?><?=$trust?>)，&nbsp;</td>
						<td style="font-family:Arial Unicode MS, 微軟正黑體;color:#003300;font-size:14px;">儲值金：$<?=number_format($balance)?>&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
<div style="display:none">
<img src="./images/btn_home.gif">
<img src="./images/btn_logout.gif">
<img src="./images/btn_home_over.gif">
<img src="./images/btn_logout_over.gif">

</div>