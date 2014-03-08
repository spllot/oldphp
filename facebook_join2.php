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
$step1 = "tick.gif";
$step2="number2.gif";
$step3="number3.gif";
$sn = $_REQUEST['member'];
$no = $_REQUEST['no'];

include './include/db_open.php';
$result = mysql_query("SELECT * FROM Product WHERE No='$no'") or die(mysql_error());
$data = mysql_fetch_array($result);


$f = fetchUrl("https://graph.facebook.com/" . $data['activity_page']);
$p = json_decode($f);
$activity_page = '<div class="fb-like-box" data-href="' . $p->{'link'} . '" data-width="189" data-show-faces="false" data-stream="false" data-header="true" style="border:solid 2px #AAAAAA;" data-height="90"></div>';









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

			FB.getLoginStatus(function(response) {
				if (response.status == 'connected') {
					var user_id = response.authResponse.userID;
					var page_id = "{$data['activity_page']}"; //coca cola
					var fql_query = "SELECT uid FROM page_fan WHERE page_id =" + page_id + " and uid=" + user_id;
					var the_query = FB.Data.query(fql_query);

					the_query.wait(function(rows) {

						if (rows.length == 1 && rows[0].uid == user_id) {
							iForm.LIKE.value = "Y";
						} else {
							iForm.LIKE.value = "N";
						}
					});
				} else {
					iForm.LIKE.value = "";
				}
			});


			 FB.Event.subscribe('edge.create',
				function(response) {window.location.reload();
			});
			 FB.Event.subscribe('edge.remove',
				function(response) {window.location.reload();
				
			});
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
	<input type="hidden" name="LIKE" value="">
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
				</tr>
				<tr style="height:62px">
					<td style="height:40px; width:50px; text-align:center"><img src="./images/{$step2}"></td>
					<td style="text-align:left">按讚加入粉絲團</td>
					<td><img src="./images/go.gif" border=0 style="cursor:pointer" onClick="Next();" alt="下一步" title="下一步"></td>
				</tr>
				<tr style="height:62px">
					<td style="height:41px; width:50px; text-align:center"><img src="./images/{$step3}"></td>
					<td style="text-align:left">留言推薦，分享朋友</td>
				</tr>
			</table>
		</td>
		<td style="width:250px; text-align:center; border:solid 5px #669900; border-left:0px">{$activity_page}</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:right; padding-top:10px"></td>
	</tr>
</table>
</center>
EOD;
?>
<script language="javascript">
	function Next(){
		if(iForm.LIKE.value == "Y"){
			window.location.href="facebook_join3.php?member=<?=$sn?>&no=<?=$no?>";
		}
		else{
			alert("請按讚加入粉絲團!");
		}
	}
</script>