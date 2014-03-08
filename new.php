<?php
include './include/session.php';

include './include/db_open.php';




$sql = "SELECT * FROM Page WHERE useFor = 'PGE_NEW'";
$result = mysql_query($sql) or die(mysql_error());
$rs = mysql_fetch_array($result);
$WEB_CONTENT = <<<EOD
<table width="100%" style="background:white">
	<tr>
		<td style="text-align:left" align="left">{$rs['Content']}</td>
	</tr>
</table>
EOD;

include './include/db_close.php';

include 'template.php';
?>