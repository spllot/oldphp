<?php
$current = basename($_SERVER["SCRIPT_NAME"]);
$deliver = (($_REQUEST['deliver']=="") ? "0":$_REQUEST['deliver']);
$c1=(($current=="seller_orders.php") ? "t_selected":"t");
$c2=(($current=="seller_orders2.php") ? "t_selected":"t");
$c3=(($current=="seller_receipt1.php") ? "t_selected":"t");
$c4=(($current=="seller_receipt2.php") ? "t_selected":"t");
$result = mysql_query("SELECT * FROM Config");
while($rs = mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}

$showimg = (($_CONFIG['showimg6'] == "Y") ? (($_CONFIG['imgurl6'] != "") ? "<center><img src='{$_CONFIG['imgurl6']}' style='width:700px; margin-bottom:10px'></center>" : "<center><img src='./upload/{$_CONFIG['ad_picpath6']}' style='width:700px; margin-bottom:10px'></center>") : "");

$tab = <<<EOD
<style>
.t{
	border:solid 1px gray;
	cursor:pointer;
	width:354px;
}

.t_selected{
	border-top:solid 1px gray;
	border-left:solid 1px gray;
	border-right:solid 1px gray;
	width:354px;
}

.t_space{
	border-bottom:solid 1px gray;
	width:10px;
}
</style>
{$showimg}
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="t_space">&nbsp;</td>
		<td class="$c1" onClick="window.location.href='seller_orders.php'" align='center'>本地商品</td>
		<td class="t_space">&nbsp;</td>
		<td class="$c2" onClick="window.location.href='seller_orders2.php'" align='center'>宅配商品</td>
		<td class="t_space">&nbsp;</td>
		<td class="$c3" onClick="window.location.href='seller_receipt1.php'" align='center'>發票處理(賣家對買家)</td>
		<td class="t_space">&nbsp;</td>
		<td class="$c4" onClick="window.location.href='seller_receipt2.php'" align='center'>發票處理(網站對賣家)</td>
		<td class="t_space">&nbsp;</td>
	</tr>
</table>
EOD;

?>