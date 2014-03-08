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
$access_token = $facebook->getAccessToken();
$profile_id = $fb_uid;		
 
$app_id = "223714571074260";
$app_secret = "f21f87199d21d6b884800034d58e34b1";

//$fb_login = '<fb:login-button autologoutlink="true" scope="manage_pages"></fb:login-button>';
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
	echo $pages;
}
else{
	echo $fb_login;
}
?>