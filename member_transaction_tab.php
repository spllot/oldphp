<?php
$y = (($_REQUEST['y'] == "") ? date('Y') : $_REQUEST['y']);
$m = (($_REQUEST['m'] == "") ? date('m') : $_REQUEST['m']);
list($y1, $m1) = explode("-", date('Y-m'));
list($y2, $m2) = explode("-", date("Y-m", strtotime("-1 month") )) ; 
list($y3, $m3) = explode("-", date("Y-m", strtotime("-2 month") )) ; 
$current = basename($_SERVER["SCRIPT_NAME"]);
$c1=(($current=="member_transaction.php") ? "t_selected":"t");
$c2=(($current=="member_transaction.php" && $y2==$y && $m2==$m) ? "t_selected":"t");
$c3=(($current=="member_transaction.php" && $y3==$y && $m3==$m) ? "t_selected":"t");
$c4=(($current=="member_transaction_atm.php") ? "t_selected":"t");
$c5=(($current=="member_transaction_exp.php") ? "t_selected":"t");

$bg1=(($current=="member_transaction.php") ? "a":"p");
$bg4=(($current=="member_transaction_atm.php") ? "a":"p");
$bg5=(($current=="member_transaction_exp.php") ? "a":"p");

$tab = <<<EOD
<style>
.t{
	color:black;
	cursor:pointer;
	height:42px;
	background-repeat:no-repeat; 
	background-position:center center;
}

.t_selected{
	color:red;
	height:42px;
	background-repeat:no-repeat; 
	background-position:center center;
}

.t_space{
	border-bottom:solid 2px #a9a9a9;
}
</style>
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="t_space">&nbsp;</td>
		<td class="$c1" onClick="window.location.href='member_transaction.php'" style='width:328px;' align='center'><img src='./images/{$bg1}_11.gif'></td><!--三個月儲值現金匯入&匯出明細-->
		<td class="t_space">&nbsp;</td>
		<td class="$c4" onClick="window.location.href='member_transaction_atm.php'" style='width:193px;' align='center'><img src='./images/{$bg4}_12.gif'></td><!--儲值現金購買回報-->
		<td class="t_space">&nbsp;</td>
		<td class="$c5" onClick="window.location.href='member_transaction_exp.php'" style='width:195px;' align='center'><img src='./images/{$bg5}_13.gif'></td><!--儲值現金匯出申請-->
		<td class="t_space">&nbsp;</td>
	</tr>
</table>
EOD;

?>