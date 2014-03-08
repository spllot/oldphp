<?php
require_once getcwd() . '/class/facebook.php';

//$fb_login = '<fb:login-button autologoutlink="true" scope="email"></fb:login-button>';


if($me){
	$fb_login = <<<EOD
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td>&nbsp;</td>
				<td style="color:white;font-size:13px;text-align:right"><img src="https://graph.facebook.com/{$fb_uid}/picture"></td>
				<td style="width:10px"></td>
				<td valign="bottom" style="color:black; font-size:10pt">{$me['name']}</td>
			</tr>
		</table>
EOD;
	
}

echo <<<EOD
	{$fb_login}
EOD;
?>