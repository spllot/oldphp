<?php
$current = basename($_SERVER["SCRIPT_NAME"]);
$c1=(($current=="seller_data.php" || $current=="member_form.php") ? "t_selected":"t");
$c2=(($current=="seller_item.php") ? "t_selected":"t");
$c5=(($current=="seller_item2.php") ? "t_selected":"t");
$c6=(($current=="seller_intro.php") ? "t_selected":"t");
$c3=(($current=="seller_question.php") ? "t_selected":"t");
$c4=(($current=="seller_status.php") ? "t_selected":"t");

$bg1=(($current=="seller_data.php") ? "a":"p");
$bg2=(($current=="seller_item.php") ? "a":"p");
$bg5=(($current=="seller_item2.php") ? "a":"p");
$bg6=(($current=="seller_intro.php") ? "a":"p");
$bg3=(($current=="seller_question.php") ? "a":"p");
$bg4=(($current=="seller_status.php") ? "a":"p");

if($_CONFIG['cashflow'] == "N")
	$cash_url = "alert('抱歉! 目前網站尚未開放金流申請功能。');";
if($_CONFIG['cashflow'] == "Y")
	$cash_url = "window.location.href='seller_data.php'";
$tab = <<<EOD
<style>
.t{
	
	cursor:pointer;
	width:113px;
	height:44px;
	background-repeat:no-repeat; 
	background-position:center center;
}

.t_selected{
	width:113px;
	height:44px;
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
		<td class="$c1" onClick="{$cash_url}" align='center'><img src='./images/{$bg1}_51.gif'></td><!--我的金流資訊-->
		<td class="t_space">&nbsp;</td>
		<td class="$c4" onClick="window.location.href='seller_status.php'" align='center'><img src='./images/{$bg4}_52.gif'></td><!--我的位置<br>與狀態設定-->
		<td class="t_space">&nbsp;</td>
		<td class="$c2" onClick="window.location.href='seller_item.php'" align='center'><img src='./images/{$bg2}_53.gif'></td><!--我的上架服務-->
		<td class="t_space">&nbsp;</td>
		<td class="$c5" onClick="window.location.href='seller_item2.php'" align='center'><img src='./images/{$bg5}_54.gif'></td><!--本地服務<br>&商品設定-->
		<td class="t_space">&nbsp;</td>
		<td class="$c6" onClick="window.location.href='seller_intro.php'" align='center'><img src='./images/{$bg6}_55.gif'></td><!--預告項目<br>與商家介紹-->
		<td class="t_space">&nbsp;</td>
		<td class="$c3" onClick="window.location.href='seller_question.php'" align='center'><img src='./images/{$bg3}_56.gif'></td><!--商品&服務<br>詢問回覆-->
		<td class="t_space">&nbsp;</td>
	</tr>
</table>
EOD;

?>