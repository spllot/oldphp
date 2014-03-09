<?php
ini_set("session.save_path", "./tmp/");
session_start();
function my_round($value, $precision=0){
	return round(round($value*pow(10, $precision+1), 0), -1)/pow(10, $precision+1);
}
$fb_desc = "InTimeGo網站是網路購物平台, 也是一個「全民皆商」的服務平台，冀望以此平台創造網路營運模式的全新改變，平台運作將把營業所得之盈餘分享給全民，只要能為平台做出貢獻的全民都能受到回饋，平台的理想願景是，推展這種商業模式可以打破唯有企業員工能分享獲利的界域，也期盼這種模式可以讓全民有更多賺錢的機會!";
?>