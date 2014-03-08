<?php
//$mode = (($_REQUEST['mode'] == "") ? "0" : $_REQUEST['mode']);
//$deliver = (($_REQUEST['deliver'] == "") ? "0" : $_REQUEST['deliver']);
//settype($mode, "int");
$current = basename($_SERVER["SCRIPT_NAME"]);
$c1=(($current=="seller_product.php") ? "t_selected":"t");
$c6=((substr($current, 0, 19)=="seller_product_step.php") ? "t_selected":"t");

$bg1=(($current=="seller_product.php") ? "a":"p");
$bg6=((substr($current, 0, 19)=="seller_product_step") ? "a":"p");

$tab = <<<EOD
<style>
.t{
	cursor:pointer;
	text-align:center;
	width:354px;
	height:42px;
	background-repeat:no-repeat; 
	background-position:center center;
}

.t_selected{
	
	text-align:center;
	width:354px;
	height:42px;
	background-repeat:no-repeat; 
	background-position:center center;
}

.t_space{
	border-bottom:solid 2px #a9a9a9;
	text-align:center;
	width:5px;
}
.t_right{
	border-bottom:solid 2px #a9a9a9;
	text-align:center;
}
</style>
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="t_space">&nbsp;</td>
		<td class="$c6" onClick="window.location.href='seller_product_step1.php'"><img src='./images/{$bg6}_61.gif'></td><!--新商品&服務提案與更新-->
		<td class="t_right">&nbsp;</td>
		<td class="$c1" onClick="window.location.href='seller_product.php?mode=1&deliver=0'"><img src='./images/{$bg1}_62.gif'></td><!--已提案之商品&服務-->
		<td class="t_space">&nbsp;</td>
	</tr>
</table>
EOD;

?>