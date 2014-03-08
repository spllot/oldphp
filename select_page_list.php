<?php
require_once getcwd() . '/class/facebook.php';
function fetchUrl($url){
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_TIMEOUT, 20);
     $retData = curl_exec($ch);
     curl_close($ch); 
 
     return $retData;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<?php
$access_token = $facebook->getAccessToken();
$profile_id = $fb_uid;		
 
$app_id = "223714571074260";
$app_secret = "f21f87199d21d6b884800034d58e34b1";

$fb_login = '<fb:login-button autologoutlink="true" scope="manage_pages"></fb:login-button>';
$page = $_REQUEST['page'];


if($me){
	$user_admin_pages = $facebook->api('/me/accounts?access_token={$access_token}');
	$pages = "<select name='activity_page' id='activity_page'><option value=''>請選擇</option>";
	for($i=0; $i<sizeof($user_admin_pages['data']); $i++){
		if($user_admin_pages['data'][$i]['perms']){
			$pages .= "<option value='{$user_admin_pages['data'][$i]['id']}'" . (($page == $user_admin_pages['data'][$i]['id']) ? " SELECTED":"") . ">{$user_admin_pages['data'][$i]['name']}</option>";
		}
	}
	$pages .= "<select>";
}
$fb_login;
?>
<body>
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
<table align="center">
	<tr>
		<td><?=$pages?></td>
		<td><?=$fb_login?></td>
	</tr>
</table>
</center>

<script language="javascript">
var page = window.dialogArguments;
if(activity_page){
	activity_page.value = page;
}

</script>