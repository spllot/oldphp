<?php
include './include/session.php';
include './include/db_open.php';
include './include/db_close.php';
$email = $_REQUEST['email'];

$WEB_CONTENT = <<<EOD
<center>
<table cellpadding="0" cellspacing="0" border="" style="width:100%; background:white; height:560px">
	<tr>
		<td style="vertical-align:top; text-align:center" valign="top" align="center">
<center>
<table border=0 width="600" align="center">
	<tr>
		<td style="border-bottom:solid 2px gray; line-height:40px; text-align:left; font-weight:bold; padding-left:10px">InTimeGo會員帳號確認</td>
	</tr>
	<tr>
		<td style="line-height:25px">
請至信箱<font color=blue>（{$email}）</font>收取會員確認信，<br>
點選確認信後，您的帳號始得生效。
		</td>
	</tr>
	<tr>
		<td style="padding:15px">
			<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="450" height="106" id="movie_name" align="middle">
			<param name="movie" value="./images/get_mail9.swf"/>
			<!--[if !IE]>-->
			<object type="application/x-shockwave-flash" data="get_mail9.swf" width="450" height="106">
			<param name="movie" value="./images/get_mail9.swf"/>
			<!--<![endif]-->
			<a href="http://www.adobe.com/go/getflash">
			<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player"/>
			</a>
			<!--[if !IE]>-->
			</object>
			<!--<![endif]-->
			</object>
		</td>
	</tr>
	<tr>
		<td style="vertical-align:bottom; border-bottom: solid 1px gray; text-align:left">
			<Table cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="./images/lamp.GIF" style="height:25px; padding-right:2px"></td>
					<td style="vertical-align:bottom">沒收到確認信該怎麼辦?</td>
				</tr>
			</table>
			
		</td>
	</tr>
	<tr>
		<td style="color:gray; text-align:left">
			若您五分鐘後無法收到會員確認信，可以在此點選<a href="javascript:Resend()">重發確認信</a>，或是以申請會員帳號之電子郵件，主旨為＂無法收到會員註冊確認信＂寄到service@intimego.com，網站管理員將為您啟用會員資格。
		</td>
	</tr>
</table>
</center>

		</td>
	<tr>
</table>
</center>
EOD;
include 'template.php';
?>
<script type="text/javascript" language="javascript">
</script>
<script language="javascript">
var counts = 0;
function Resend(){
	if(counts == 0){
		counts ++;
		$.post(
			'member_resend.php',
			{
				email: '<?=$email?>'
			},
			function(data)
			{
				eval("var response = " + data);
				if(response.err == "0"){
					alert("確認信已發送到「<?=$email?>」，請檢查您的信箱!");
					window.location.reload();
				}
				else{
					alert("找不到符合的資料，可能帳號已啟用!");
					window.location.href="member_login.php";
				}
			}
		);
	}
	else{
		alert("重發確認信處理中，請耐心等候!");
	}
}
</script>