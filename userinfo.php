<?php
include './include/session.php';

include './include/db_open.php';
$balance=0;
$result = mysql_query("SELECT COALESCE(SUM(Amount), 0) as Amount FROM logTransaction WHERE Owner='" . $_SESSION['member']['userID'] . "'");
if($rs=mysql_fetch_array($result)){
	$balance = $rs['Amount'];
}
include './include/db_close.php';

if(empty($_SESSION['member'])){
//	echo "Hi, 遊客&nbsp;";
}
else{
//	echo "Hi, {$_SESSION['member']['Nick']}，(等級<font color=red>{$_SESSION['member']['Level']}</font>)&nbsp;儲值金：<font color=red>&#36;{$balance}&nbsp;</font>";
}


//exit;
if(empty($_SESSION['member'])){
	echo <<<EOD
			<div>
			<table cellpadding="0" cellspacing="0" border="0" align="center">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" align="right" width="516">
							<tr>
								<td class="menu51" style="width:294px; text-align:center; color:#993300; font-family:新細明體; font-size:14px;">Hi, 遊客&nbsp;</td>
								<td class="menu5" id="menu5" onClick="mCli1(this, 'member_login.php', 5);" onMouseOver="mOvr1(this, 5);" onMouseOut="mOut1(this, 5);"style="background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td>
								<td class="menu6" id="menu6" onClick="mCli1(this, 'member_register.php', 6);" onMouseOver="mOvr1(this, 6);" onMouseOut="mOut1(this, 6);"style="background-repeat:no-repeat; width:111px; height:29px; background-position:center center; cursor:pointer">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<div id="menu" style="width:146px;position:absolute; display:none; padding:0px; text-align:center; background:#ffffcc; border-radius: 8px; border:solid 5px gray; z-index:99" onMouseOver="showMenu();" onMouseOut="hideMenu();">
				<table cellpadding="0" cellspcing="0" border="0" width="100%">
					<tr>
						<td style="padding: 10px; padding-top: 0px">
							<div style="text-align:right; padding-bottom:5px"><a href="member.php"><img src="./images/btn_member_over.gif" border=0></a></div>
							<div><a href="member_login.php" target="iContent">請登入系統</a></div>
						</td>
					</tr>
				</table>
			</div>
			</div>
EOD;
}
else{
	$dis = "!";
	if($_SESSION['member']){
		$seller_menu1 = ' onMouseOver="showMenu();"';
		$seller_menu2 = ' onMouseOver="showMenu();" onMouseOut="hideMenu();"';
		if($_SESSION['member']['Seller'] == 2){
			$dis = "";
		}
	}
	else{
		$seller_menu1 = " onMouseOver=\"this.src='./images/btn_member_over.gif';\" onMouseOut=\"this.src='./images/btn_member.gif';\"";
	}
	echo <<<EOD
			<div>
			<table cellpadding="0" cellspacing="0" border="0" align="right">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" border="0" align="right" width="516">
							<tr>
								<td class="menu51" style="width:294px; color:#993300; font-family:新細明體; font-size:14px;; text-align:center;">Hi, {$_SESSION['member']['Nick']}(等級{$_SESSION['member']['Level']})，&nbsp;儲值金：&#36;{$balance}&nbsp;</td>
								<td style="width:111px"><a href="javascript:Logout();"><img src="./images/btn_logout.gif" border=0 onMouseOver="this.src='./images/btn_logout_over.gif';" onMouseOut="this.src='./images/btn_logout.gif';"></a></td>
								<td style="width:111px"><a href="member.php"><img id="member_link" src="./images/btn_member.gif" border=0{$seller_menu1}></a></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			</div>
			<div style="; float:right; text-align:right">
			</div>
			<div id="menu" style="width:146px;position:absolute; display:none; padding:0px; text-align:center; background:#ffffcc; border-radius: 8px; border:solid 5px gray"{$seller_menu2}>
				<table cellpadding="0" cellspcing="0" border="0" width="100%">
					<tr>
						<td style="padding: 10px; padding-top: 0px">
							<div style="text-align:center; padding-bottom:5px"><a href="member.php"><img src="./images/btn_member_over.gif" border=0></a></div>
							<div><a href="member.php?menu=10">業者資訊中心</a></div>
							<div><{$dis}a href="member.php?menu=12">商品交易紀錄</a></div>
							<div><{$dis}a href="member.php?menu=13">商家處理資訊</a></div>
							<div><{$dis}a href="member.php?menu=14">匯款帳務查詢</a></div>
						</td>
					</tr>
				</table>
			</div>

EOD;
}
?>
<div style="display:none">
<img src="./images/btn_login.gif">
<img src="./images/btn_logout.gif">
<img src="./images/btn_register.gif">
<img src="./images/btn_member.gif">
<img src="./images/btn_login_over.gif">
<img src="./images/btn_logout_over.gif">
<img src="./images/btn_register_over.gif">
<img src="./images/btn_member_over.gif">
</div>