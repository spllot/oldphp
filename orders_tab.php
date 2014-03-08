<?php
$current = basename($_SERVER["SCRIPT_NAME"]);
$deliver = (($_REQUEST['deliver']=="") ? "0":$_REQUEST['deliver']);
$c1=(($deliver=="0") ? "t_selected":"t");
$c2=(($deliver=="1") ? "t_selected":"t");
$c1=(($current=="orders.php") ? "t_selected":"t");
$c2=(($current=="orders2.php") ? "t_selected":"t");

$bg1=(($current=="orders.php") ? "a":"p");
$bg2=(($current=="orders2.php") ? "a":"p");

$tab = <<<EOD
<style>
.t{
	cursor:pointer;
	width:354px;
	height:42px;
	background-repeat:no-repeat; 
	background-position:center center;
}

.t_selected{
	width:354px;
	height:42px;
	background-repeat:no-repeat; 
	background-position:center center;
}

.t_space{
	border-bottom:solid 2px #a9a9a9;
	width:10px;
}
</style>
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="t_space">&nbsp;</td>
		<td class="$c1" onClick="window.location.href='orders.php'" align='center'><img src='./images/{$bg1}_41.gif'></td><!--到店商品-->
		<td class="t_space">&nbsp;</td>
		<td class="$c2" onClick="window.location.href='orders2.php'" align='center'><img src='./images/{$bg2}_42.gif'></td><!--宅配商品-->
		<td class="t_space">&nbsp;</td>
	</tr>
</table>
EOD;

?>