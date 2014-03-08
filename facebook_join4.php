<?php
require_once getcwd() . '/class/facebook.php';
$step1="tick.gif";
$step2 = "tick.gif";
$step3="tick.gif";
$sn = $_REQUEST['member'];
$no = $_REQUEST['no'];
$member_display="none";
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Member WHERE No='$sn'") or die(mysql_error());
$member = mysql_fetch_array($result);
$result = mysql_query("SELECT * FROM Product WHERE No='$no'") or die(mysql_error());
$data = mysql_fetch_array($result);

$join = "0";

$result = mysql_query("SELECT * FROM logActivity WHERE Product='{$no}' AND fbID='{$fb_uid}'") or die(mysql_error());
if(mysql_num_rows($result) > 0){
	$join = "1";
}
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

if($member){
	$member_display = "block";
}
include './include/db_close.php';

echo <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<link type="text/css" href="style.css" rel="stylesheet" />

<div id="fb-root"></div>
<script>
			window.fbAsyncInit = function() {
			  FB.init({
				appId      : '223714571074260',
				status     : true, 
				cookie     : true,
				xfbml      : true,
				oauth      : true
			  });
			  FB.Event.subscribe('auth.login', function() {
				window.location.reload();
			  });
			  FB.Event.subscribe('auth.logout', function() {
				window.location.reload();
			  });
			 FB.Event.subscribe('edge.create',
				function(response) {
					//window.location.reload();
				}
			);
		};
		(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/zh_TW/all.js#xfbml=1&appId=223714571074260";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));


</script>
<center>
<form name="iForm" method="post" action="facebook_join4_save.php">
<input type="hidden" name="sn" value="{$sn}">
<input type="hidden" name="no" value="{$no}">
<div style="height:100px"></div>
<table cellpadding="0" cellspacing="0" style="width:700px">
	<tr>
		<td style="text-align:center; border:solid 5px #669900;">
			<table width="100%">
				<tr>
					<td style="text-align:left; padding-left:5px; background:#f5f5f5; line-height:30px">參加活動：{$data['Name']}</td>
				</tr>
				<tr>
					<td style="text-align:left; padding-top:10px;">
						請設定中獎通知信箱：
						<br>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:20px">
						<table align="center">
							<tr>
								<td style="border-right:solid 1px #f5f5f5; padding:5px">{$fb_login}</td>
								<td>
									<table cellpadding="0" cellspacing="0" border="0">
										<tr style="display:{$member_display}">
											<td><input type="radio" name="e" value="1"></td>
											<td style="text-align:left;">InTimeGo 會員信箱：{$member['userID']}</td>
										</tr>
										<tr>
											<td><input type="radio" name="e" value="2"></td>
											<td style="text-align:left;">FaceBook 會員信箱：{$me['email']}</td>
										</tr>
										<tr>
											<td><input type="radio" name="e" value="3"></td>
											<td style="text-align:left;">其它信箱：<input type="text" name="email" size="35"></td>
										</tr>
									</table>
								</td>
							</tR>
						</table>
					</td>
				</tR>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:right; padding-top:10px"><img src="./images/go.gif" border=0 style="cursor:pointer" onClick="Next();" alt="下一步" title="下一步"></td>
	</tr>
</table>
</form>
</center>
EOD;
?>
<script language="javascript">
	function Next(){
		var iForm = document.iForm;
		if(!iForm.e[0].checked && !iForm.e[1].checked && !iForm.e[2].checked){
			alert("請選擇您要使用的信箱!");
		}
		else if(iForm.e[2].checked && !iForm.email.value){
			alert("請輸入其它信箱!");
			iForm.email.focus();
		}
		else{
			iForm.submit();
		}
	}
</script>