<?php
require_once getcwd() . '/class/facebook.php';
$fb_user = '<fb:login-button autologoutlink="true" scope="email"></fb:login-button>';
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
		<td style="width:250px; text-align:center; border:solid 5px #669900; border-left:0px"><table align="center"><Tr><td>{$fb_user}</td></tr></table></td>
	</tr>
</table>
</center>
EOD;
?><script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
<script language="javascript">
</script>

<?if(!$me){?>

<script language="javascript">
	var counts = 0;
	function fb_check(){
		window.location.reload();
		setTimeout("fb_check()", 500);
	}
</script>

<script language="javascript">setTimeout("fb_check()", 500);</script>
<?}?>