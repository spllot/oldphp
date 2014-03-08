<?php
include './include/session.php';
include './include/db_open.php';
include 'profit_tab.php';
$WEB_CONTENT = <<<EOD
<table style="width:100%" cellpadding="0" cellspacing="0">
	<tr style="height:10px"></tr>
	<tr>
		<td style="padding:10px; text-align:center; font-size:14pt">會員獲利公告</td>
	</tr>
	<tr>
		<td>{$tab}</td>
	</tr>
	<tr>
		<td align="center" style="text-align:center">
		<center>




		</center>
		</td>
	</tr>
</table>

<br>
<br>
<br>

EOD;




include './include/db_close.php';
include 'template.php';
?>

