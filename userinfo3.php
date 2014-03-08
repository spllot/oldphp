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
			<div class="menu52" style="text-align:left; width:100%; color:#993300; font-family:Arial Unicode MS,微軟正黑體; font-size:14px;">　Hi, 遊客&nbsp;
			</div>
EOD;
}
else{
	if($_SESSION['member']['Seller']==2){
		$seller_menu1 = ' onMouseOver="showMenu();"';
		$seller_menu2 = ' onMouseOver="showMenu();" onMouseOut="hideMenu();"';
	}
	else{
		$seller_menu1 = " onMouseOver=\"this.src='./images/btn_member_over.gif';\" onMouseOut=\"this.src='./images/btn_member.gif';\"";
	}
	echo <<<EOD
			<div class="menu52" style="width:100%; color:#993300; font-family:Arial Unicode MS,微軟正黑體; font-size:14px;; text-align:left;">　Hi, {$_SESSION['member']['Nick']}(等級{$_SESSION['member']['Level']})，&nbsp;儲值金：&#36;{$balance}&nbsp;
			</div>

EOD;
}
?>