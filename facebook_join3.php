<?php
require_once getcwd() . '/class/facebook.php';
$step1="tick.gif";
$step2 = "tick.gif";
$step3="number3.gif";
$sn = $_REQUEST['member'];
$no = $_REQUEST['no'];
include './include/db_open.php';
$result = mysql_query("SELECT * FROM Product WHERE No='$no'") or die(mysql_error());
$data = mysql_fetch_array($result);
$url = "http://{$WEB_HOST}/";//product4_detail.php?no={$no}"

$fb_title = "【{$data['Name']}】{$discount}折";
$fb_desc = $data['Description'];
$fb_thumb = "http://{$WEB_HOST}/upload/{$data['Photo']}";

if($data['Mode'] == 2){
	if($data['Deliver'] == 0){
		$url .= "product4_detail.php?no={$no}";
	}
	else{
		$url .= "product5_detail.php?no={$no}";
	}
}
else if($data['Mode'] == 1){
	if($data['Deliver'] == 0){
		$url .= "product1_detail.php?no={$no}";
	}
	else{
		$url .= "product2_detail.php?no={$no}";
	}
}

$share = <<<EOD
		<table cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td><img src="./images/icops_1.gif"></td>
				<td><img src="./images/icops_5.gif" style="cursor:pointer" onClick="postToFeed();"></td>
			</tr>
		</table>
EOD;

$result = mysql_query("SELECT * FROM logShare WHERE fbID='$fb_uid' AND Product='$no'");
if(mysql_num_rows($result) > 0){
	$s="Y";
	$share = "已分享";
	echo "window.location.href='facebook_join4.php';";
	exit;
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
<form name="iForm">
	<input type="hidden" name="s" value="{$s}">
</form>
<div style="height:80px"></div>
<table cellpadding="0" cellspacing="0" align="center">
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
				</tr>
				<tr style="height:62px">
					<td style="height:41px; width:50px; text-align:center"><img src="./images/{$step3}"></td>
					<td style="text-align:left">留言推薦，分享朋友</td>
					<td><img src="./images/go.gif" border=0 style="cursor:pointer" onClick="Next();" alt="下一步" title="下一步"></td>
				</tr>
			</table>
		</td>
		<td style="width:250px; text-align:center; border:solid 5px #669900; border-left:0px">{$share}</td>
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
		if(iForm.s.value=="Y"){
			window.location.href="facebook_join4.php?member=<?=$sn?>&no=<?=$no?>";
		}
		else{
			alert("請留言推薦，分享朋友");
		}
	}
</script>
<script>
	function move_to_top( value ){
		$(".fb_dialog").each(function(index) {
			if($(this).css("top")!='-10000px')
			{
				$(this).css("top", '50px' );
				window.parent.document.body.scrollTop = 0;
				window.parent.document.documentElement.scrollTop = 0;
			}
		});
		setTimeout( ('move_to_top("'+value+'");'), 1250);
	}
	
	function postToFeed() {
		var caption = encodeURI("<?=$url?>") ;

        var obj = {
			method: 'feed',
			link: '<?=$url?>',
            picture: '<?=$fb_thumb?>',
            name: '<?=$fb_title?>',
//            caption: caption,
            description: '<?=$fb_desc?>'
		};

        function callback(response) {
			//window.parent.document.body.scrollTop = 0;
			//window.parent.document.documentElement.scrollTop = 0;
			//alert(response);
			if(response){
				window.location.href="facebook_join4.php?member=<?=$sn?>&no=<?=$no?>";
				/*
				$.post(
					'fackbook_share.php',
					{
						no: '{$no}'
					},
					function(data)
					{
						eval("var r = " + data);
						if(r.success == "1"){
							window.location.reload();
						}
					}
				);
				*/
			}
        }

		FB.Canvas.scrollTo(0,0);
        FB.ui(obj, callback);
		$(".fbProfileBrowserResult").ready( function(){
			t = setTimeout ( ('move_to_top("'+50+'")'), 1250 );
		});
      }
</script>