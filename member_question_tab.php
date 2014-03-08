<?php
$current = basename($_SERVER["SCRIPT_NAME"]);
$c1=(($current=="member_question.php") ? "t_selected":"t");
$c2=(($current=="member_question1.php") ? "t_selected":"t");
$c3=(($current=="member_question2.php") ? "t_selected":"t");

$bg1=(($current=="member_question.php") ? "a":"p");
$bg2=(($current=="member_question1.php") ? "a":"p");
$bg3=(($current=="member_question2.php") ? "a":"p");

$tab = <<<EOD
<style>
.t{
	
	cursor:pointer;
	width:230px;
	height:42px;
	background-repeat:no-repeat; 
	background-position:center center;
}

.t_selected{
	width:230px;
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
		<td class="$c1" onClick="window.location.href='member_question.php'" align='center'><img src='./images/{$bg1}_31.gif'></td><!--全部諮詢-->
		<td class="t_space">&nbsp;</td>
		<td class="$c2" onClick="window.location.href='member_question1.php'" align='center'><img src='./images/{$bg2}_32.gif'></td><!--未回覆諮詢-->
		<td class="t_space">&nbsp;</td>
		<td class="$c3" onClick="window.location.href='member_question2.php'" align='center'><img src='./images/{$bg3}_33.gif'></td><!--已回覆諮詢-->
		<td class="t_space">&nbsp;</td>
	</tr>
</table>
EOD;

?>