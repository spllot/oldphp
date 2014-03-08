<?php
//include './include/session.php';
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

$sn = $_REQUEST['member'];
$no = $_REQUEST['no'];
include './include/db_open.php';

$result = mysql_query("SELECT * FROM Member WHERE No='$sn'") or die(mysql_error());
$member = mysql_fetch_array($result);
$result = mysql_query("SELECT * FROM Product WHERE No='$no'") or die(mysql_error());
$data = mysql_fetch_array($result);
$url = "http://{$WEB_HOST}/";//product4_detail.php?no={$no}"

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


$join = "0";

$result = mysql_query("SELECT * FROM logActivity WHERE Product='{$no}' AND fbID='{$fb_uid}'") or die(mysql_error());
if(mysql_num_rows($result) > 0){
	$join = "1";
}


$step1="0";
$step2="0";
$step3="0";

//print_r($fb_uid);

if($me){
	$step1 = "1";
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
	
	try {
	  $facebook->setAccessToken($accessToken);
		$likes = $facebook->api("/me/likes/{$data['activity_page']}");
		if( !empty($likes['data']) )
			$step2="1";
	} catch (FacebookApiException $e) {
		error_log($e);
	}
	$result = mysql_query("SELECT * FROM logShare WHERE fbID='$fb_uid' AND Product='$no'");
	if(mysql_num_rows($result) > 0){
		  $step3="1";
	}
include './include/db_close.php';
}
?>

<?
$member_display="none";
if($member){
	$member_display = "block";
}
//echo $step1 . $step2 . $step3 . $join;

if($step1=="1" && $step2=="1" && $step3=="1"){
	if($join!="1"){
		echo <<<EOD
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
		};
		(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/zh_TW/all.js#xfbml=1&appId=223714571074260";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
			<center>
			<form name="iForm" method="post" action="facebook_join_save.php" target="iAction9">
			<input type="hidden" name="product" value="{$no}">
			<input type="hidden" name="member" value="{$sn}">
			<table style="width:700px">
				<tr>
					<td style="text-align:left; padding-left:5px; background:#f5f5f5; line-height:30px">參加活動：{$data['Name']}</td>
				</tr>
				<tr>
					<td style="text-align:left; padding-top:10px;">
						請設定中獎通知信箱：
					</td>
				</tr>
				<tr>
					<td style="text-align:left; padding-top:10px; padding-bottom:10px">
						<table>
							<tr>
								<td style="border-right:solid 1px #f5f5f5; padding:5px">{$fb_login}</td>
								<td>
									<table cellpadding="0" cellspacing="0" border="0">
										<tr style="display:{$member_display}">
											<td><input type="radio" name="e" value="1"></td>
											<td>使用 InTimeGo 會員信箱：{$member['userID']}</td>
										</tr>
										<tr>
											<td><input type="radio" name="e" value="2"></td>
											<td>使用 FaceBook 會員信箱：{$me['email']}</td>
										</tr>
										<tr>
											<td><input type="radio" name="e" value="3"></td>
											<td>使用其它信箱：<input type="text" name="email" size="40"></td>
										</tr>
									</table>
								</td>
							</tR>
						</table>
					</td>
				</tr>
				<tr>
					<td style="text-align:right; line-height:60px; height:60px; border-top:solid 2px #f5f5f5">
						<input type="button" value="確定參加活動" onClick="Save();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="button" value="取消" onClick="setClose();">
					</td>
				</tr>
			</table>
			</center>
				
			</form>
				<iframe name="iAction9" style="width:100%; height:100px;display:none"></iframe>
<script language="javascript">
			function setURL(){
				parent.iContent.location.href='{$url}';
				setClose();
			}
			function setClose(){
				parent.$.fn.colorbox.close();
			}
			function Save(){
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
EOD;
	}
	else{
//		echo "111";
	}
}
else{
//	echo "222";
}
?>
