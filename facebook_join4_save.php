<?php
require_once getcwd() . '/class/facebook.php';
require_once './class/tools.php';
require_once './class/javascript.php';
JavaScript::setCharset("UTF-8");


$product = $_REQUEST['no'];
$sn = $_REQUEST['sn'];
$no = $product;
$e = $_REQUEST['e'];
$email = $_REQUEST['email'];


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


$join = false;

$result = mysql_query("SELECT * FROM logActivity WHERE Product='{$no}' AND fbID='{$fb_uid}'") or die(mysql_error());
if(mysql_num_rows($result) > 0){
	$join = true;
}


$step1=true;
$step2=true;
$step3=true;




if($member && $e == 1){
	$email = $member['userID'];
}
if($e == 2){
	$email = $me['email'];
}


if(!Tools::checkEMail($email)){JavaScript::Alert("電子郵件格式錯誤!!".$email);exit;}


if($step1 && $step2 && $step3){
	if(!$join){
		$sql = "INSERT INTO logActivity SET Product='$product', fbID='{$fb_uid}', fbName='{$me['name']}', dateJoined=CURRENT_TIMESTAMP, EMail='$email', Member='{$member['No']}'";
		//echo $sql;
		mysql_query($sql) or die(mysql_error());
		JavaScript::Alert("成功參加活動，祝您中獎!");
	}
	else{
		JavaScript::Alert("系統已有你的參加記錄，一個 Facebook 帳號只能參加一次!");
	}
}
else{
	JavaScript::Alert("您尚未登入Facebook或網路異常，請待候再試!");
}

include './include/db_close.php';
?>
<script language="javascript">
			function setURL(){
				parent.iContent.location.href='<?=$url?>';
				setClose();
			}
			function setClose(){
				parent.$.fn.colorbox.close();
			}
</script>
<script language="javascript">setURL();</script>
