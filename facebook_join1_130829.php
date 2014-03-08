<?php
require_once getcwd() . '/class/facebook.php';
$fb_user = '<fb:login-button autologoutlink="true" scope="email"></fb:login-button>';
$join = false;
$sn = $_REQUEST['member'];
$no = $_REQUEST['no'];
if($me){    
//echo "<script language='javascript'>window.location.href='facebook_join2.php';</script>";
	include './include/db_open.php';
	$result = mysql_query("SELECT * FROM logActivity WHERE Product='{$no}' AND fbID='{$fb_uid}'") or die(mysql_error());
	if(mysql_num_rows($result) > 0){
		$join = true;
	}
	include './include/db_close.php';
	$fb_user = <<<EOD
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td rowspan=2>&nbsp;</td>
				<td rowspan=2 style="color:white;font-size:13px;text-align:right"><img src="https://graph.facebook.com/{$fb_uid}/picture"></td>
				<td rowspan=2 style="width:10px"></td>
				<td style="text-align:left">{$fb_user}</td>
			</tr>
			<tr>
				<td valign="bottom" style="color:black; font-size:10pt">{$me['name']}</td>
			</tr>
		</table>
EOD;
}
$step1="number1.gif";
$step2="number2.gif";
$step3="number3.gif";
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
	  
	  FB.getLoginStatus(function(response) {
		  if (response.status === 'connected') {
			  document.iForm.login.value = "Y";
		  } else if (response.status === 'not_authorized') {
			  document.iForm.login.value = "N";
		  } else {
			  document.iForm.login.value = "";
		  }
	  });
	  	  
	  FB.Event.subscribe('edge.create',
				function(response) {
					window.location.reload();
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
<form name="iForm">
	<input type="hidden" name="login" value="">
</form>
<div style="height:80px"></div>
<table cellpadding="0" cellspacing="0">
	<tr>
		<td style="width:450px; border:solid 5px #669900">
			<table style="" cellpadding="0" cellspacing="0">
				<tr style="height:62px">
					<td rowspan="3" style="width:100px; text-align:center"><img src="./images/join_group_word.gif"></td>
					<td style="height:40px; width:50px; text-align:center"><img src="./images/{$step1}"></td>
					<td style="text-align:left">登入FaceBook</td>
					<td><img src="./images/go.gif" border=0 style="cursor:pointer" onClick="Next();" alt="下一步" title="下一步"></td>
				</tr>
				<tr style="height:62px">
					<td style="height:40px; width:50px; text-align:center"><img src="./images/{$step2}"></td>
					<td style="text-align:left">按讚加入粉絲團</td>
				</tr>
				<tr style="height:62px">
					<td style="height:41px; width:50px; text-align:center"><img src="./images/{$step3}"></td>
					<td style="text-align:left">留言推薦，分享朋友</td>
				</tr>
			</table>
		</td>
		<td style="width:250px; text-align:center; border:solid 5px #669900; border-left:0px"><table align="center"><Tr><td>{$fb_user}</td></tr></table></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:right; padding-top:10px"></td>
	</tr>
</table>

</center>
EOD;
?><script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
<script language="javascript">
	function Next(){
<? if($join){?>
		alert("系統已有你的參加記錄，一個 Facebook 帳號只能參加一次!");
<? }else{?>
		if(document.iForm.login.value=="Y"){
			window.location.href="facebook_join2_130829.php?member=<?=$sn?>&no=<?=$no?>";
		}
		else{
			alert("請先登入Facebook!");
		}
<? }?>
	}
</script>

<? if(!$me){ ?>

<script language="javascript">
	var counts = 0;
	function fb_reload(){
		window.location.reload();
	}
	function fb_check(){
		counts ++;
		/*
		alert(counts);
		*/
		$.post(
			'fackbook_check.php',
			{
				action: 'moving',
				counts: counts
			},
			function(data)
			{
//				alert(data);
				eval("var r = " + data);
				if(r.login == "Y"){
					window.location.reload();
				}
			}
		);		
//		setTimeout("fb_check()", 500);
	}
</script>




<? }?>