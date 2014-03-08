<?php
require_once getcwd() . '/class/facebook.php';



$no = $_REQUEST['no'];
include './include/db_open.php';
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

if($me){
	mysql_query("INSERT INTO logShare SET fbID='$fb_uid', Product='$no'");
	$feedback['success'] = 1;
}


include './include/db_close.php';
echo json_encode($feedback);
?>