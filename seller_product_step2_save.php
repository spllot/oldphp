<?php
include './include/session.php';
require_once './class/tools.php';
require_once './class/javascript.php';
JavaScript::setCharset("UTF-8");
if(empty($_SESSION['member'])){
	JavaScript::setCharset("UTF-8");
	JavaScript::Alert("您尚未登入!");
	JavaScript::Execute("window.parent.location.reload();");
	exit;
}

$no = $_REQUEST['no'];
$status = $_REQUEST['status'];
$broadcast = $_REQUEST['broadcast'];
$referral = $_REQUEST['referral'];
$editor = $_REQUEST['editor'];
$agree = $_REQUEST['agree'];
$deliver = $_REQUEST['deliver'];
$area = $_REQUEST['area'];
$catalog = $_REQUEST['catalog'];
$catalog2 = $_REQUEST['catalog2'];
$catalog3 = $_REQUEST['catalog3'];
$type = $_REQUEST['type'];
$daysbeforereserve = $_REQUEST['daysbeforereserve'];
$daysunit = $_REQUEST['daysunit'];
$price = $_REQUEST['price'];
$price1 = $_REQUEST['price1'];
$daysonsale = $_REQUEST['daysonsale'];
$quota = $_REQUEST['quota'];
$name = $_REQUEST['name'];
$description = $_REQUEST['description'];
$photo = $_REQUEST['pic'];
$datevalidate = $_REQUEST['datevalidate'];
$dateexpire = $_REQUEST['dateexpire'];
$hours = $_REQUEST['hours'];
$restrict = $_REQUEST['restrict'];
$use2 = $_REQUEST['use2'];
$buy3 = $_REQUEST['buy3'];
$use3 = $_REQUEST['use3'];
$buy4 = $_REQUEST['buy4'];
$memo = $_REQUEST['memo'];
$special1 = $_REQUEST['special1'];
$special2 = $_REQUEST['special2'];
$special3 = $_REQUEST['special3'];
$special4 = $_REQUEST['special4'];
$special5 = $_REQUEST['special5'];
$seller = $_REQUEST['seller'];
$url = $_REQUEST['url'];
$phone = $_REQUEST['phone'];
$receipt = $_REQUEST['receipt'];
$intro = $_REQUEST['intro'];
$about = $_REQUEST['about'];
$openhours = $_REQUEST['openhours'];
$address = str_replace("臺", "台", $_REQUEST['address']);
$map = $_REQUEST['map'];
$isdonate = $_REQUEST['isdonate'];
$slide = $_REQUEST['slide'];
$slide2 = $_REQUEST['slide2'];
$slide3 = $_REQUEST['slide3'];
$slide4 = $_REQUEST['slide4'];
$donate = $_REQUEST['donate'];
$used = $_REQUEST['used'];
$sale = $_REQUEST['sale'];
$cashflow = $_REQUEST['cashflow'];
$amount = $_REQUEST['amount'];
$duration = $_REQUEST['duration'];




$activity = $_REQUEST['activity'];
$activity_page = $_REQUEST['activity_page'];
$activity_start = $_REQUEST['activity_start'];
$activity_end = $_REQUEST['activity_end'];
$activity_ann = $_REQUEST['activity_ann'];
$activity_min = $_REQUEST['activity_min'];
$activity_per = $_REQUEST['activity_per'];
$activity_draw = $_REQUEST['activity_draw'];
$activity_info = $_REQUEST['activity_info'];
$activity_holder = $_REQUEST['activity_holder'];
$activity_email = $_REQUEST['activity_email'];
$activity_quota = $_REQUEST['activity_quota'];

                    




$latitude = explode(",", str_replace(array("(", ")", " "), "", $_REQUEST['latitude']));

if($name == "" || $price1 =="" || $price == "") exit;

//if($activity==1){$price1 = 0;}

$discount = 10*($price1/$price);



include './include/db_open.php';

switch($restrict){
	case 2:
		$maxbuy = 1;
		$maxuse = $use2;
		break;
	case 3:
		$maxbuy = $buy3;
		$maxuse = $use3;
		break;
	case 4:
		$maxbuy = $buy4;
		break;
	default:
		$maxbuy = 1;
		$maxuse = 1;
		break;
}
$date1 = date('Y-m-d');
$date2 = date("Y-m-d", strtotime(date("Y-m-d", strtotime($activity_start)) . " +30 day"));
$date3 = date("Y-m-d", strtotime(date("Y-m-d", strtotime($activity_start)) . " +2 day"));
$date4 = date("Y-m-d", strtotime(date("Y-m-d", strtotime($activity_end)) . " +7 day"));



if($broadcast == 1 && $referral == $_SESSION['member']['Phone']){
	JavaScript::Alert("商品之介紹人不可填寫賣家本身!");
	exit;
	$result = mysql_query("SELECT * FROM Product WHERE Member='" . $_SESSION['member']['No'] . "' AND Broadcast=1");
	if(mysql_num_rows($result) == 0){
	}
}



if($activity == 1){
	if($activity_start < $date1){
		JavaScript::Alert("活動期間設定錯誤：開始日期不可輸入過去之時間!");
		exit;
	}
	if($activity_end < $date3){
		JavaScript::Alert("活動期間不可短於3天!");
		exit;
	}
	if($activity_end > $date2){
		JavaScript::Alert("活動期間不可超過30天!");
		exit;
	}
	if($activity_ann > $date4){
		JavaScript::Alert("公佈日期需設定活動結束後一周內的時間!");
		exit;
	}
}





$date1 = date('Y-m-d');
$date2 = date("Y-m-d", strtotime(date("Y-m-d", strtotime($datevalidate)) . " +{$daysonsale} day"));
$date3 = date("Y-m-d", strtotime(date("Y-m-d", strtotime($datevalidate)) . " +90 day"));

/*
if($datevalidate < $date1){
	JavaScript::Alert("兌換期間設定錯誤：開始日期不可輸入過去之時間!");
	exit;
}
*/
if($dateexpire < $date2){
	JavaScript::Alert("兌換期間不可短於販售期間($daysonsale)天)!");
	exit;
}
if($dateexpire > $date3){
	JavaScript::Alert("兌換期間不可超過三個月(90天)!");
	exit;
}
if($status == 1 && $photo == ""){
	JavaScript::Alert("請上傳商品圖示!");
	exit;
}

$limit = array(0, 4, 4, 8, 8, 16, 16, 32, 32);
/*
if($status == 1 && $cashflow == 0){
//if(1==1){
	$sql = "SELECT COUNT(*) FROM Product WHERE dateClose > CURRENT_TIMESTAMP AND dateApprove <> '0000-00-00 00:00:00' AND Member='" . $_SESSION['member']['No'] . "' AND Cashflow=0";
	$result = mysql_query($sql) or die(mysql_error());
	list($counts) = mysql_fetch_row($result);
	if($limit[$_SESSION['member']['Level']] <= $counts){
		JavaScript::Alert("您的等級 {$_SESSION['member']['Level']} 只能提案 {$limit[$_SESSION['member']['Level']]} 個非金流商品!");
		exit;
	}
}
*/

if($no != ""){
	$sql = "UPDATE Product SET `Catalog2`='$catalog2', `Catalog3`='$catalog3', Broadcast='$broadcast', Amount='$amount', Duration='$duration', Used='$used', Sale='$sale', Cashflow='$cashflow', `Referral`='$referral', `Editor`='$editor', `Deliver`='$deliver', `Area`='$area', `Catalog`='$catalog', `Type`='$type', `daysBeforeReserve`='$daysbeforereserve', `daysUnit`='$daysunit', `Price`='$price', `Price1`='$price1', `Discount`='$discount', `daysOnSale`='$daysonsale', `Quota`='$quota', `Name`='$name', `Description`='$description', `Photo`='$photo', `dateValidate`='$datevalidate', `dateExpire`='$dateexpire', `Hours`='$hours', `Restrict`='$restrict', `maxBuy`='$maxbuy', `maxUse`='$maxuse', `Memo`='$memo', `Special1`='$special1', `Special2`='$special2', `Special3`='$special3', `Special4`='$special4', `Special5`='$special5', `Intro`='$intro', `Seller`='$seller', `Url`='$url', `Phone`='$phone', `Receipt`='$receipt', `About`='$about', `openHours`='$openhours', `Address`='$address', `Latitude`='" . $latitude[0] . "', `Longitude`='" . $latitude[1] . "', `Status`='$status', `dateUpdate`=CURRENT_TIMESTAMP, updateBy='" . $_SESSION['member']['Name'] . "', Map='$map', isDonate = '$donate', Slide='$slide', Slide2='$slide2', Slide3='$slide3', Slide4='$slide4', Activity = '$activity', activity_page = '$activity_page', activity_start = '$activity_start', activity_end = '$activity_end', activity_ann = '$activity_ann', activity_min = '$activity_min', activity_per = '$activity_per', activity_draw = '$activity_draw', activity_info = '$activity_info', activity_holder = '$activity_holder', activity_email = '$activity_email', activity_quota = '$activity_quota' WHERE Member='" . $_SESSION['member']['No'] . "' AND No='$no'";
	mysql_query($sql)or die(mysql_error());
}
else{
	$sql = "INSERT INTO `Product` (Catalog2, Catalog3, Broadcast, Amount, Duration, Sale, Used, Cashflow, `Referral`, `Editor`, `Deliver`, `Area`, `Catalog`, `Type`, `daysBeforeReserve`, `daysUnit`, `Price`, `Price1`, `Discount`, `daysOnSale`, `Quota`, `Name`, `Description`, `Photo`, `dateValidate`, `dateExpire`, `Hours`, `Restrict`, `maxBuy`, `maxUse`, `Memo`, `Special1`, `Special2`, `Special3`, `Special4`, `Special5`, `Intro`, `Seller`, `Url`, `Phone`, `Receipt`, `About`, `openHours`, `Address`, `Latitude`, `Longitude`, `Status`, `dateCreate`, `createBy`, `dateUpdate`, `updateBy`, `Member`, Mode, Map, isDonate, Slide, Slide2, Slide3, Slide4, Activity, activity_page, activity_start, activity_end, activity_ann, activity_min, activity_per, activity_draw, activity_info, activity_holder, activity_email, activity_quota) VALUES ('$catalog2', '$catalog3', '$broadcast', '$amount', '$duration', '$sale', '$used', '$cashflow', '$referral', '$editor', '$deliver', '$area', '$catalog', '$type', '$daysbeforereserve', '$daysunit', '$price', '$price1', '$discount', '$daysonsale', '$quota', '$name', '$description', '$photo', '$datevalidate', '$dateexpire', '$hours', '$restrict', '$maxbuy', '$maxuse', '$memo', '$special1', '$special2', '$special3', '$special4', '$special5', '$intro', '$seller', '$url', '$phone', '$receipt', '$about', '$openhours', '$address', '" . $latitude[0] . "', '" . $latitude[1] . "', '$status', CURRENT_TIMESTAMP, '" . $_SESSION['member']['Name'] . "', CURRENT_TIMESTAMP, '" . $_SESSION['member']['Name'] . "', '" . $_SESSION['member']['No'] . "', 1, '$map', '$donate', '$slide', '$slide2', '$slide3', '$slide4', '$activity', '$activity_page', '$activity_start', '$activity_end', '$activity_ann', '$activity_min', '$activity_per', '$activity_draw', '$activity_info', '$activity_holder', '$activity_email', '$activity_quota')";
	mysql_query($sql)or die(mysql_error());
	$no = mysql_insert_id();
	mysql_query("UPDATE logUpload SET Product=$no WHERE Product=0 AND Member='" . $_SESSION['member']['No'] . "'") or die(mysql_error());

}


$result = mysql_query("SELECT * FROM Config");
while($rs=mysql_fetch_array($result)){
	$_CONFIG[$rs['ID']] = $rs['YN'];
}
$upload = 0;
$result = mysql_query("SELECT COUNT(*) FROM logUpload WHERE Member='" . $_SESSION['member']['No'] . "' AND Product = $no");
if(list($upload) = mysql_fetch_row($result)){}
/*
echo "cashflow=" . $cashflow . "<bR>";
echo "upload=" . $upload . "<bR>";

echo "pics1=" .  $_CONFIG['pics1'] . "<br>";
echo "pics2=" .  $_CONFIG['pics2'] . "<br>";
*/

if($cashflow == 1 && $upload > $_CONFIG['pics1']){
	$result = mysql_query("SELECT * FROM logUpload WHERE Member='" . $_SESSION['member']['No'] . "' AND Product = $no");
	$i=0;
	while($rs = mysql_fetch_array($result)){
		$i++;
		if($i > $_CONFIG['pic1']){
			mysql_query("DELETE FROM logUpload  WHERE Member='" . $_SESSION['member']['No'] . "' AND No = '" . $rs['No'] . "'");
			unlink("./upload/" . $rs['Path']);
		}
	}
}
if($cashflow == 0 && $upload > $_CONFIG['pics2']){
	$result = mysql_query("SELECT * FROM logUpload WHERE Member='" . $_SESSION['member']['No'] . "' AND Product = $no");
	$i=0;
	while($rs = mysql_fetch_array($result)){
		$i++;
		if($i > $_CONFIG['pic2']){
			mysql_query("DELETE FROM logUpload  WHERE Member='" . $_SESSION['member']['No'] . "' AND No = '" . $rs['No'] . "'");
			unlink("./upload/" . $rs['Path']);
		}
	}

}

JavaScript::setCharset("UTF-8");
JavaScript::Alert("資料已儲存!");

//echo "<pre>" . $sql;
//print_r($_REQUEST);

if($activity == 1)
	JavaScript::setURL("seller_product.php?activity=$activity", "window.parent");
else
	JavaScript::setURL("seller_product.php?mode=1&deliver=$deliver", "window.parent");
include './include/db_close.php';

?>
