<?php
$current = basename($_SERVER["SCRIPT_NAME"]);
$c1=(($current=="profit.php") ? "t_selected":"t");
$c2=(($current=="profit2.php") ? "t_selected":"t");
$c3=(($current=="profit3.php") ? "t_selected":"t");
$c4=(($current=="profit4.php") ? "t_selected":"t");

$bg1=(($current=="profit.php") ? "a":"p");
$bg2=(($current=="profit2.php") ? "a":"p");
$bg3=(($current=="profit3.php") ? "a":"p");
$bg4=(($current=="profit4.php") ? "a":"p");

$tab = <<<EOD
<style>
.t{
	
	cursor:pointer;
	width:158px;
	line-height:43px;	 
	background-repeat:no-repeat; 
	background-position:center center;	
}

.t_selected{
	
	width:158px;
	line-height:43px;	
	background-repeat:no-repeat; 
	background-position:center center;
}

.t_space{
	border-bottom:solid 2px #a9a9a9;
	width:10px;
	line-height:41px;
}
</style>
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="t_space">&nbsp;</td>
		<td class="$c1" onClick="window.location.href='profit.php'" align='center'><img src='./images/{$bg1}_menux301.gif'></td><!--商品粉絲抽獎活動-->
		<td class="t_space">&nbsp;</td>
		<td class="$c2" onClick="window.location.href='profit2.php'" align='center'><img src='./images/{$bg2}_menux311.gif'></td><!--部落格行銷獎勵金-->
		<td class="t_space">&nbsp;</td>
		<td class="$c3" onClick="window.location.href='profit3.php'" align='center'><img src='./images/{$bg3}_menux321.gif'></td><!--商品傳播紅利金-->
		<td class="t_space">&nbsp;</td>
		<td class="$c4" onClick="window.location.href='profit4.php'" align='center'><img src='./images/{$bg4}_menux331.gif'></td><!--公益基金&捐助-->
		<td class="t_space">&nbsp;</td>
	</tr>
</table>
EOD;

?>