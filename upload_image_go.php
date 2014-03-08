<?php
ini_set("session.save_path", "./tmp/");
session_start();
require_once './class/javascript.php';
require_once './class/tools.php';
JavaScript::setCharset("UTF-8");

$extension = Tools::getExts($_FILES['imgurl']['name']);
$target = time() . "." . $extension;
$w = Tools::parseInt2($_REQUEST['w'], 240);
$h = Tools::parseInt2($_REQUEST['h'], 160);




$size = filesize($_FILES['imgurl']['tmp_name']);
if($size >250*1024){
	JavaScript::Alert("上傳圖片不可超過250K!!");
	JavaScript::Execute("window.close();");
	exit;
}





if(copy($_FILES['imgurl']['tmp_name'], "./upload/" . $target)){
	$iconurl = "http://" . $_SERVER['SERVER_NAME'] . substr($_SERVER['PHP_SELF'], 0, Tools::lastIndexOf($_SERVER['PHP_SELF'], "/") + 1) . "./upload/" . $target;
	/*
	$big_image = getcwd() . "/./upload/" . $target;
	list($width, $height, $type, $attr) = getimagesize($big_image);
	$size1 = $width . "x" . $height;
	$size2 = $w . "x" . $h;

	$small_image = getcwd() . "/./upload/" . $target;
//	$exec_str="convert -define jpeg:size=$size1 $big_image  -thumbnail $size2" . "^ -gravity center -extent $size2  $small_image";
//	exec($exec_str);
	if($w/$h > $width/$height){ //較高
		$size = $w . "x" . $height;
	}
	else{  //較寬
		$size = $width . "x" . $h;
	}
	$exec_str="convert '-geometry' $size $big_image $small_image";
	exec($exec_str);
	list($width, $height, $type, $attr) = getimagesize($small_image);
	
	$size = $width . "x" . $height;
	if($w/$h > $width/$height){//較高
		$offset = ($height - $h)/2;
		$exec_str1="convert '-crop' $size+0+" . $offset . " $small_image $small_image";
		$size = $width . "x" . ($height -$offset);
		$exec_str2="convert '-crop' $size+0-" . $offset . " $small_image $small_image";
	}
	else{//較寬
		$offset = ($width - $w)/2;
		$exec_str1="convert '-crop' $size+" . $offset . "+0 $small_image $small_image";
		$size = ($width - $offset) . "x" . $height;
		$exec_str2="convert '-crop' $size-" . $offset . "+0 $small_image $small_image";
	}
	exec($exec_str1);
	exec($exec_str2);
	*/

	include './include/db_open.php';
	mysql_query("INSERT INTO logUpload SET Product='" . $_SESSION['PRODUCT'] . "', Member='" . $_SESSION['member']['No'] . "', Path='" . basename($iconurl) . "', dateUploaded=CURRENT_TIMESTAMP") or die(mysql_error());

	include './include/db_close.php';

	$_SESSION['UPLOAD_COUNTS'] ++;
	JavaScript::Execute("window.returnValue = '$iconurl';");
}
else{
	JavaScript::Alert("上傳圖片不可超過250K!!");
}
JavaScript::Execute("window.close();");

?>
