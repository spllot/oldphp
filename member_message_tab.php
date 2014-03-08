<?php
$current = basename($_SERVER["SCRIPT_NAME"]);
$c1=(($current=="member_message.php") ? "t_selected":"t");
$c2=(($current=="member_message1.php") ? "t_selected":"t");
$c3=(($current=="member_message2.php") ? "t_selected":"t");
$c4=(($current=="member_message3.php") ? "t_selected":"t");

$bg1=(($current=="member_message.php") ? "a":"p");
$bg2=(($current=="member_message1.php") ? "a":"p");
$bg3=(($current=="member_message2.php") ? "a":"p");
$bg4=(($current=="member_message3.php") ? "a":"p");

$tab = <<<EOD
<style>
.t{

	cursor:pointer;
	width:171px;
	height:42px;
	background-repeat:no-repeat; 
	background-position:center center;
}

.t_selected{

	width:171px;
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
		<td class="$c1" onClick="window.location.href='member_message.php'" align='center'><img src='./images/{$bg1}_21.gif'></td><!--未讀訊息-->
		<td class="t_space">&nbsp;</td>
		<td class="$c2" onClick="window.location.href='member_message1.php'" align='center'><img src='./images/{$bg2}_22.gif'></td><!--私人訊息-->
		<td class="t_space">&nbsp;</td>
		<td class="$c3" onClick="window.location.href='member_message2.php'" align='center'><img src='./images/{$bg3}_23.gif'></td><!--系統訊息-->
		<td class="t_space">&nbsp;</td>
		<td class="$c4" onClick="window.location.href='member_message3.php'" align='center'><img src='./images/{$bg4}_24.gif'></td><!--公共訊息-->
		<td class="t_space">&nbsp;</td>
	</tr>
</table>
EOD;

?>